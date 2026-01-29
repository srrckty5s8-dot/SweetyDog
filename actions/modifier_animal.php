<?php
header('Location: /sweetydog/public/index.php?c=animal&a=edit&id=' . urlencode($_GET['id'] ?? ''));
exit;
