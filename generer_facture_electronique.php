<?php
session_start();
error_reporting(0);
ini_set('display_errors', 0);

require_once 'config/db.php';
require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use Atgp\FacturX\Writer;

if (!isset($_SESSION['admin_connecte'])) die("Accès refusé");

// --- LECTURE DU FICHIER code.env ---
$apiKey = null;
$apiUrl = "https://api.n2f.com/v1/expenses/upload";
$envPath = __DIR__ . '/code.env';

if (file_exists($envPath)) {
    $content = file_get_contents($envPath);
    if (preg_match('/N2F_API_KEY\s*=\s*([^\s#]+)/', $content, $matches)) {
        $apiKey = trim($matches[1], "\"' ");
    }
    if (preg_match('/N2F_API_URL\s*=\s*([^\s#]+)/', $content, $matches)) {
        $apiUrl = trim($matches[1], "\"' ");
    }
}

if (!$apiKey) die("❌ Erreur : API Key manquante.");

$id_prestation = $_GET['id'] ?? null;

$query = $bdd->prepare("
    SELECT p.*, a.id_animal, a.nom_animal, a.espece, prop.nom, prop.prenom, prop.telephone 
    FROM Prestations p 
    JOIN Animaux a ON p.id_animal = a.id_animal 
    JOIN Proprietaires prop ON a.id_proprietaire = prop.id_proprietaire 
    WHERE p.id_prestation = ?
");
$query->execute([$id_prestation]);
$data = $query->fetch(PDO::FETCH_ASSOC);

if (!$data) die("Prestation introuvable.");

// 2. Génération du PDF (Ici tu peux garder l'adresse complète dans le visuel !)
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans'); 
$dompdf = new Dompdf($options);

ob_start();
include __DIR__ . '/views/facture_view.php';
$html = ob_get_clean();

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdfOutput = $dompdf->output();

// 3. XML Factur-X (Profil MINIMUM - Strictement restreint au CountryID)
$dateSoin = date('Ymd', strtotime($data['date_soin']));
$idFacture = date('Y') . '-' . $data['id_prestation'];
$prix = number_format($data['prix'], 2, '.', '');

$xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rsm:CrossIndustryInvoice xmlns:rsm="urn:un:unece:uncefact:data:standard:CrossIndustryInvoice:100" xmlns:ram="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100" xmlns:udt="urn:un:unece:uncefact:data:standard:UnqualifiedDataType:100">
  <rsm:ExchangedDocumentContext>
    <ram:GuidelineSpecifiedDocumentContextParameter>
      <ram:ID>urn:factur-x.eu:1p0:minimum</ram:ID>
    </ram:GuidelineSpecifiedDocumentContextParameter>
  </rsm:ExchangedDocumentContext>
  <rsm:ExchangedDocument>
    <ram:ID>$idFacture</ram:ID>
    <ram:TypeCode>380</ram:TypeCode>
    <ram:IssueDateTime>
      <udt:DateTimeString format="102">$dateSoin</udt:DateTimeString>
    </ram:IssueDateTime>
  </rsm:ExchangedDocument>
  <rsm:SupplyChainTradeTransaction>
    <ram:ApplicableHeaderTradeAgreement>
      <ram:SellerTradeParty>
        <ram:Name>SweetyDog41</ram:Name>
        <ram:SpecifiedLegalOrganization>
          <ram:ID schemeID="0002">12345678901234</ram:ID>
        </ram:SpecifiedLegalOrganization>
        <ram:PostalTradeAddress>
          <ram:CountryID>FR</ram:CountryID>
        </ram:PostalTradeAddress>
      </ram:SellerTradeParty>
      <ram:BuyerTradeParty>
        <ram:Name>{$data['prenom']} {$data['nom']}</ram:Name>
      </ram:BuyerTradeParty>
    </ram:ApplicableHeaderTradeAgreement>
    <ram:ApplicableHeaderTradeDelivery/>
    <ram:ApplicableHeaderTradeSettlement>
      <ram:InvoiceCurrencyCode>EUR</ram:InvoiceCurrencyCode>
      <ram:SpecifiedTradeSettlementHeaderMonetarySummation>
        <ram:TaxBasisTotalAmount currencyID="EUR">$prix</ram:TaxBasisTotalAmount>
        <ram:GrandTotalAmount currencyID="EUR">$prix</ram:GrandTotalAmount>
        <ram:DuePayableAmount currencyID="EUR">$prix</ram:DuePayableAmount>
      </ram:SpecifiedTradeSettlementHeaderMonetarySummation>
    </ram:ApplicableHeaderTradeSettlement>
  </rsm:SupplyChainTradeTransaction>
</rsm:CrossIndustryInvoice>
XML;

try {
    $writer = new Writer();
    $factureHybride = $writer->generate($pdfOutput, $xml);

    $nomFichier = "Facture_SweetyDog_" . $idFacture . ".pdf";
    $chemin = __DIR__ . '/factures/' . $nomFichier;
    if (!is_dir(__DIR__ . '/factures/')) mkdir(__DIR__ . '/factures/', 0777, true);
    
    file_put_contents($chemin, $factureHybride);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new CURLFile($chemin, 'application/pdf', $nomFichier)]);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: ApiKey $apiKey"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    header("Location: /sweetydog/public/animals/" . (int)$data['id_animal'] . "/tracking?success=1");

    exit();

} catch (Exception $e) {
    die("🔥 Erreur finale : " . $e->getMessage());
}