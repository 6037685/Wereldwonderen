<?php
require_once 'vendor/autoload.php';
require_once 'db/conectie.php';
session_start();

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

// Controleer of gebruiker ingelogd is en rol geldig is
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['beheerder', 'onderzoeker'])) {
    die("Toegang geweigerd");
}

// Haal wonderen op afhankelijk van rol
if ($_SESSION['user_role'] === 'beheerder') {
    $stmt = $pdo->query("SELECT w.*, u.name AS added_by_name FROM wonders w LEFT JOIN users u ON w.added_by = u.id ORDER BY w.id ASC");
} else { // onderzoeker
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT w.*, u.name AS added_by_name FROM wonders w LEFT JOIN users u ON w.added_by = u.id WHERE w.added_by = ? ORDER BY w.id ASC");
    $stmt->execute([$userId]);
}

$wereldwonderen = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Maak Word-document
$phpWord = new PhpWord();
$section = $phpWord->addSection();
$section->addText('Overzicht Wereldwonderen', ['bold' => true, 'size' => 16]);
$section->addTextBreak(1);

foreach ($wereldwonderen as $wonder) {
    $section->addText("Naam: " . $wonder['name'], ['bold' => true, 'size' => 14]);

    // Voeg foto toe indien aanwezig en goedgekeurd
    if (!empty($wonder['photo']) && $wonder['photo_approved'] == 1 && file_exists($wonder['photo'])) {
        $section->addImage($wonder['photo'], [
            'width' => 300,
            'height' => 200,
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
        ]);
    }

    $section->addText("Locatie: " . $wonder['location']);
    $section->addText("Beschrijving: " . $wonder['description']);
    $section->addText("Tags: " . $wonder['tags']);
    $section->addText("Bouwjaar: " . $wonder['bouwjaar']);
    $section->addText("Historische info: " . $wonder['historische_info']);
    $section->addText("Type: " . $wonder['type']);
    $section->addText("Continent: " . $wonder['continent']);
    $section->addText("Bestaat nog: " . ($wonder['bestaat_nog'] ? 'Ja' : 'Nee'));
    $section->addText("Toegevoegd door: " . $wonder['added_by_name']);
    $section->addText("Gemaakt op: " . $wonder['created_at']);
    $section->addText("Views: " . $wonder['views']);

    $section->addPageBreak(); // Elke wonder op een nieuwe pagina
}

// Download als Word-bestand
$filename = $_SESSION['user_role'] === 'beheerder' ? 'wereldwonderen.docx' : 'mijn_wereldwonderen.docx';
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save('php://output');
exit;
