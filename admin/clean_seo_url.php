<?php
require('includes/application_top.php');

$message = '';
// Speichern
if ($_POST['action']==='save') {
    $type = xtc_db_input($_POST['type']);
    $ref  = (int)$_POST['ref_id'];
    $url  = xtc_db_input($_POST['seo_url']);
    $lang = xtc_db_input($_POST['language_code']);
    xtc_db_query("
      REPLACE INTO xt_seo_url 
        (type, ref_id, language_code, seo_url)
      VALUES 
        ('$type', $ref, '$lang', '$url')
    ");
    $message = 'Eintrag gespeichert';
}

// Alle Einträge laden
$entries = xtc_db_query("SELECT * FROM xt_seo_url ORDER BY type, ref_id");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Clean SEO URL</title>
  <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
  <?php if ($message): ?>
    <div class="success"><?= $message ?></div>
  <?php endif ?>
  <h1>Clean SEO URL Verwaltung</h1>
  <form method="post">
    <input type="hidden" name="action" value="save">
    <label>Typ
      <select name="type">
        <option value="category">Kategorie</option>
        <option value="product">Produkt</option>
        <option value="manufacturer">Hersteller</option>
        <option value="content">Inhalt</option>
      </select>
    </label>
    <label>Ref-ID <input type="number" name="ref_id" required></label>
    <label>Sprache <input name="language_code" value="de" size="2"></label>
    <label>SEO-URL <input name="seo_url" required></label>
    <button type="submit">Speichern</button>
  </form>

  <h2>Vorhandene Einträge</h2>
  <table>
    <tr><th>ID</th><th>Typ</th><th>Ref</th><th>Spr.</th><th>URL</th></tr>
    <?php while ($row = xtc_db_fetch_array($entries)): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['type'] ?></td>
        <td><?= $row['ref_id'] ?></td>
        <td><?= $row['language_code'] ?></td>
        <td><?= htmlspecialchars($row['seo_url']) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>
<?php require('includes/application_bottom.php'); ?>
