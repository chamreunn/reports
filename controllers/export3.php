<?php
session_start();
require_once '../vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

$getid = $_GET['id'] ?? null;
$getregulator = $_GET['regulator'] ?? null;

if (!$getid || !$getregulator) {
  die('Missing required parameters.');
}

include('../config/dbconn.php');

$stmt = $dbh->prepare("SELECT headline, data FROM tblreport_step3 WHERE id = :id");
$stmt->bindParam(':id', $getid, PDO::PARAM_INT);
$stmt->execute();
$insertedData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$insertedData) {
  die('No data found for the provided ID.');
}

$phpWord = new PhpWord();
$sectionStyle = array(
  'marginTop' => 1400,
  'marginBottom' => 1400,
  'marginLeft' => 1400,
  'marginRight' => 1400,
);
$phpWord->setDefaultParagraphStyle(
  array(
    'alignment' => Jc::BOTH,
    'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12),
    'spacing' => 0,
  )
);

$paragraphStyle = array(
  'alignment' => Jc::CENTER,
  'spaceAfter' => 0,
);

$coverPageStyle = array(
  'marginTop' => 1000,
  'marginBottom' => 1000,
  'marginLeft' => 300,
  'marginRight' => 300,
);
$coverSection = $phpWord->addSection($coverPageStyle);

$coverSection->addText('ព្រះរាជាណាចក្រកម្ពុជា', array('name' => 'Khmer MEF2', 'size' => 14, 'color' => '#2F5496'), $paragraphStyle);
$coverSection->addText('ជាតិ សាសនា ព្រះមហាក្សត្រ', array('name' => 'Khmer MEF1', 'size' => 14, 'color' => '#2F5496'), $paragraphStyle);

$imgUrl = '../assets/img/icons/brands/logo2.png';
$textbox = $coverSection->addTextBox(
  array(
    'width' => 200,
    'height' => 200,
    'alignment' => Jc::LEFT,
    'marginLeft' => 300,
    'borderColor' => 'none',
    'borderSize' => 0,
  )
);

$textbox->addImage(
  $imgUrl,
  array(
    'width' => 100,
    'height' => 100,
    'alignment' => Jc::CENTER
  )
);

$textRun = $textbox->addTextRun(array('alignment' => Jc::CENTER));
$textRun->addText('អាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ', array('name' => 'Khmer MEF2', 'size' => 10, 'color' => '#2F5496'));
$textRun->addText("\n");
$textRun->addText('អង្គភាពសវនកម្មផ្ទៃក្នុង', array('name' => 'Khmer MEF2', 'size' => 10, 'color' => '#2F5496'));
$textRun->addText("\n");
$textRun->addText('លេខ:......................អ.ស.ផ.', array('name' => 'Khmer MEF2', 'size' => 10, 'color' => '#2F5496'));

$additionalTextLines = [
  'របាយការណ៍សវនកម្ម',
  'នៅ ' . htmlspecialchars_decode($getregulator), // Decode HTML entities
  'នៃអាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ'
];

for ($i = 0; $i < 3; $i++) {
  $coverSection->addTextBreak();
}

foreach ($additionalTextLines as $index => $line) {
  $additionalTextRun = $coverSection->addTextRun(array('alignment' => Jc::CENTER));
  $additionalTextRun->addText($line, array('name' => 'Khmer MEF2', 'size' => 22, 'color' => '#2F5496'));
  if ($index !== count($additionalTextLines) - 1) {
    $additionalTextRun->addText("\n", array(), array('spaceAfter' => 0));
  }
}

function convertToKhmerNumeric($number)
{
  $khmerNumerals = array(
    '0' => '០',
    '1' => '១',
    '2' => '២',
    '3' => '៣',
    '4' => '៤',
    '5' => '៥',
    '6' => '៦',
    '7' => '៧',
    '8' => '៨',
    '9' => '៩'
  );

  $khmerNumber = '';
  $numberArray = str_split($number);
  foreach ($numberArray as $digit) {
    $khmerNumber .= isset($khmerNumerals[$digit]) ? $khmerNumerals[$digit] : $digit;
  }

  return $khmerNumber;
}

$currentYearKhmer = convertToKhmerNumeric(date('Y'));

$additionalText = "សម្រាប់ឆ្នាំ " . $currentYearKhmer;

$additionalTextRun = $coverSection->addTextRun(array('alignment' => Jc::CENTER, 'marginTop' => 720));
for ($i = 0; $i < 15; $i++) {
  $additionalTextRun->addTextBreak(null, 1);
}
$additionalTextRun->addText($additionalText, array('name' => 'Khmer MEF2', 'size' => 22, 'color' => '#2F5496'), $paragraphStyle);

$disclaimerSection = $phpWord->addSection($sectionStyle);

$disclaimerSection->addTextBreak(10);
$disclaimerSection->addText('សេចក្តីប្រកាសបដិសេធ', array('name' => 'Khmer MEF2', 'size' => 22, 'color' => '#2F5496'), array('alignment' => Jc::CENTER));

$disclaimerText = 'អង្គភាពសវនកម្មផ្ទៃក្នុងនៃអាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ (អ.ស.ហ.) មិនទទួលខុសត្រូវចំពោះទិន្នន័យនិងព័ត៌មានស្តីពីការអនុវត្តការប្រមូលចំណូល ការអនុវត្តចំណាយ ការបង់ភាគទាន និងការប្រើប្រាស់ភាគទាននៅក្នុងរបាយការណ៍ស្ដីពីការពិនិត្យឡើងវិញ ការអនុវត្តការប្រមូលចំណូល ការអនុវត្តចំណាយ ការបង់ភាគទាន និងការប្រើប្រាស់ភាគទាននេះទេ។ អង្គភាពក្រោមឱវាទ អ.ស.ហ. ត្រូវទទួលខុសត្រូវចំពោះភាពពេញលេញ ភាពគ្រប់គ្រាន់ និងភាពត្រឹមត្រូវនៃទិន្នន័យនិងព័ត៌មានស្តីពីការអនុវត្តការប្រមូលចំណូល ការអនុវត្តចំណាយ ការបង់ភាគទាន និងការប្រើប្រាស់ភាគទាននៅក្នុងរបាយការណ៍ស្ដ';

$disclaimerSection->addText($disclaimerText, array('name' => 'Khmer MEF1', 'size' => 12), array('alignment' => Jc::BOTH));

$tocSection = $phpWord->addSection();
$tocSection->addText('មាតិកា', array('name' => 'Khmer MEF2', 'size' => 12, 'bold' => true), array('alignment' => Jc::CENTER));
$tocSection->addTOC(array('name' => 'Khmer MEF2', 'size' => 12));

$contentSection = $phpWord->addSection($sectionStyle);

$phpWord->addTitleStyle(1, array('name' => 'Khmer MEF2', 'size' => 16, 'color' => '#2F5496'), array('alignment' => Jc::BOTH));

if ($insertedData) {
  $headlines = explode("\n", $insertedData['headline']);
  $data = explode("\n", $insertedData['data']);

  foreach ($headlines as $index => $headline) {
    $cleanHeadline = preg_replace('/^(&nbsp;|\s)+/', '', htmlspecialchars_decode($headline));
    $cleanData = isset($data[$index]) ? preg_replace('/^(&nbsp;|\s)+/', '', html_entity_decode(strip_tags(trim($data[$index])))) : '';

    $contentSection->addTitle($cleanHeadline, 1);

    if ($cleanData) {
      $contentSection->addText($cleanData, array('name' => 'Khmer MEF1', 'size' => 12), array('alignment' => Jc::BOTH));
    }
  }
}

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="របាយការណ៍សវនកម្ម_' . htmlspecialchars_decode($getregulator) . '.docx"');

$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('php://output');
