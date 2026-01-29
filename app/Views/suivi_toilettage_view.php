
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suivi - <?= htmlspecialchars($animal['nom_animal'] ?? 'Animal'); ?></title>

    <!-- ✅ CSS : chemin ABSOLU (sinon cassé sur /animals/33/tracking) -->
    <link rel="stylesheet" href="/sweetydog/assets/style.css">


    <style>
        .prestation-selector { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 8px; }
        .prestation-selector input[type="checkbox"] { display: none; }
        .prestation-label {
            padding: 8px 16px; background: #f0f0f0; border: 2px solid #ddd;
            border-radius: 20px; cursor: pointer; font-size: 0.9em; transition: all 0.3s ease;
            color: #555;
        }
        .prestation-selector input[type="checkbox"]:checked + .prestation-label {
            background-color: var(--vert-moyen); border-color: var(--vert-fonce); color: white;
        }
        .tag-soin {
            background: #e8f5e9; color: #2e7d32; padding: 4px 10px; border-radius: 6px;
            font-size: 0.8em; font-weight: 600; border: 1px solid #c8e6c9; margin-right: 5px;
        }
        .info-bandeau {
            background: #fff; padding: 15px; border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;
            display: flex; gap: 20px; flex-wrap: wrap; border: 1px solid #eee;
        }
        .info-box { flex: 1; min-width: 120px; }
        .info-box strong { display: block; color: #7f8c8d; font-size: 0.75rem; text-transform: uppercase; }
        .btn-download-pdf {
            text-decoration: none;
            background: #e8f5e9;
            color: #2e7d32;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85em;
            font-weight: bold;
            border: 1px solid #c8e6c9;
        }
        .btn-download-pdf:hover { background: #c8e6c9; }
    </style>
</head>
<body>

<?php if (isset($_GET['success']) && (int)$_GET['success'] === 1): ?>
    <div id="success-banner" style="background: #d4edda; color: #155724; padding: 20px; border: 1px solid #c3e6cb; border-radius: 8px; margin: 20px auto; max-width: 800px; text-align: center; font-family: sans-serif;">
        <h2 style="margin: 0 0 10px 0;">✅ Encaissement validé !</h2>
        <p style="margin: 5px 0;">La facture Factur-X a été générée et transmise à N2F.</p>

        <?php if (!empty($download_link)): ?>
            <p style="font-size: 0.9em; color: #666;">
                Le téléchargement de la facture va démarrer... <br>
                <small>Si rien ne se passe, <a href="<?= htmlspecialchars($download_link) ?>" download style="color: #155724; text-decoration: underline;">cliquez ici pour la récupérer</a>.</small>
            </p>

            <script>
                window.addEventListener('load', function() {
                    setTimeout(function() {
                        const link = document.createElement('a');
                        link.href = <?= json_encode($download_link) ?>;
                        link.download = <?= json_encode($nomFichierPDF ?? 'facture.pdf') ?>;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }, 500);
                });
            </script>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="container-large">
    <div class="header-flex" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="margin-bottom: 5px;">🧼 Dossier de <?= htmlspecialchars($animal['nom_animal'] ?? ''); ?></h2>
            <span style="color: #7f8c8d;">
                Propriétaire : <strong><?= htmlspecialchars(($animal['prenom'] ?? '') . ' ' . ($animal['nom'] ?? '')); ?></strong>
            </span>
        </div>

        <!-- ✅ Retour : utiliser une route -->
        <a href="<?= route('clients.index') ?>" class="btn-edit" style="text-decoration: none; background: #eee; color: #333;">← Retour</a>
    </div>

    <div class="info-bandeau">
        <div class="info-box">
            <strong>Animal / Race</strong>
            🐾 <?= htmlspecialchars($animal['espece'] ?? ''); ?> (<?= htmlspecialchars($animal['race'] ?? 'Inconnue'); ?>)
        </div>
        <div class="info-box">
            <strong>Poids</strong>
            ⚖️ <?= !empty($animal['poids']) ? htmlspecialchars($animal['poids']).' kg' : '---'; ?>
        </div>
        <div class="info-box">
            <strong>Stérilisé</strong>
            <?php if(($animal['steril'] ?? 0) == 1): ?>
                <span style="color: #2e7d32; font-weight: bold;">✅ OUI</span>
            <?php else: ?>
                <span style="color: #e67e22; font-weight: bold;">❌ NON</span>
            <?php endif; ?>
        </div>
        <div class="info-box">
            <strong>Téléphone</strong>
            📞 <?= htmlspecialchars($animal['telephone'] ?? '---'); ?>
        </div>
    </div>

    <div class="container" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 40px;">
        <h3 style="margin-bottom: 20px; color: var(--vert-fonce);">Nouvelle visite</h3>

        <!-- ✅ ACTION : chemin ABSOLU -->
       <form action="<?= route('prestations.store', ['id' => $animal['id_animal']]) ?>" method="POST">
    <!-- champs du formulaire -->


            <input type="hidden" name="id_animal" value="<?= htmlspecialchars($animal['id_animal'] ?? ''); ?>">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label>Date de la visite</label>
                    <input type="date" name="date_soin" value="<?= date('Y-m-d'); ?>" required>
                </div>
                <div>
                    <label>Prix de la prestation (€)</label>
                    <input type="number" step="0.01" name="prix" placeholder="ex: 45.00" required>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label>Types de soins réalisés</label>
                <div class="prestation-selector">
                    <input type="checkbox" name="type_soin[]" id="bain" value="Bain Brush">
                    <label for="bain" class="prestation-label">Bain Brush</label>

                    <input type="checkbox" name="type_soin[]" id="coupe" value="Coupe Ciseaux">
                    <label for="coupe" class="prestation-label">Coupe Ciseaux</label>

                    <input type="checkbox" name="type_soin[]" id="tonte" value="Tonte">
                    <label for="tonte" class="prestation-label">Tonte</label>

                    <input type="checkbox" name="type_soin[]" id="epilation" value="Épilation">
                    <label for="epilation" class="prestation-label">Épilation</label>

                    <input type="checkbox" name="type_soin[]" id="griffes" value="Coupe Griffes">
                    <label for="griffes" class="prestation-label">Coupe Griffes</label>

                    <input type="checkbox" name="type_soin[]" id="retouche" value="Retouche">
                    <label for="retouche" class="prestation-label">Retouche</label>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label>Notes & Observations</label>
                <textarea name="notes" rows="3" style="width: 100%; border: 1px solid #ddd; border-radius: 8px; padding: 12px;" placeholder="État du poil, comportement..."></textarea>
            </div>

            <button type="submit" style="background: var(--vert-fonce); color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-weight: bold;">
                Valider l'Encaissement
            </button>
        </form>
    </div>

    <h3 style="margin-bottom: 15px;">Historique des soins</h3>
    <table style="background: white; border-radius: 12px; overflow: hidden; border-collapse: separate; border-spacing: 0; width: 100%;">
        <thead style="background: #f8f9fa;">
            <tr>
                <th style="padding: 15px; text-align: left;">Date</th>
                <th style="text-align: left;">Prestations</th>
                <th style="text-align: left;">Observations</th>
                <th style="text-align: left;">Prix</th>
                <th style="text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if(empty($historique)): ?>
            <tr><td colspan="5" style="padding: 20px; text-align: center;">Aucun soin enregistré.</td></tr>
        <?php else: ?>
            <?php foreach ($historique as $soin): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px;"><strong><?= date('d/m/Y', strtotime($soin['date_soin'])); ?></strong></td>
                    <td>
                        <?php
                        $tags = explode(", ", $soin['type_soin'] ?? '');
                        foreach($tags as $tag){
                            $tag = trim($tag);
                            if($tag !== '') echo "<span class='tag-soin'>" . htmlspecialchars($tag) . "</span>";
                        }
                        ?>
                    </td>
                    <td style="color: #7f8c8d; font-size: 0.9em;"><?= htmlspecialchars($soin['notes'] ?? '-'); ?></td>
                    <td><span style="font-weight: bold; color: #2e7d32;"><?= number_format((float)$soin['prix'], 2, ',', ' '); ?> €</span></td>

                   <td style="text-align: center; white-space: nowrap;">
  <?php
    $idPrest = (int)($soin['id_prestation'] ?? 0);
    $annee   = date('Y', strtotime($soin['date_soin'] ?? 'now'));

    // 📄 PDF stocké dans /sweetydog/factures/
    $file_rel = "factures/Facture_SweetyDog_{$annee}-{$idPrest}.pdf";

    // URL publique
    $pdf_url = "/sweetydog/" . $file_rel;

    // Chemin disque réel (pour file_exists)
    $pdf_fs = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $pdf_url;
  ?>

  <?php if ($idPrest > 0 && file_exists($pdf_fs)) : ?>
    <a href="<?= htmlspecialchars($pdf_url) ?>"
       target="_blank"
       class="btn-download-pdf"
       title="Ouvrir la facture PDF">
      📄 Facture
    </a>
  <?php else : ?>
    <a href="<?= route('invoices.generate', ['id' => $idPrest]) ?>" title="Générer la facture" style="text-decoration:none; font-size:1.1em;">⚙️</a>

  <?php endif; ?>

  <span title="Prestation verrouillée"
        style="margin-left: 10px; cursor: help; filter: grayscale(100%); opacity: 0.5;">🔒</span>
</td>


                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
