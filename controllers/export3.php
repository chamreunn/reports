<?php
session_start();
// Include Composer's autoloader
require_once '../vendor/autoload.php';

// Import the PhpWord namespace
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

// Get the ID parameter from the URL
$getid = $_GET['id'];
$getregulator = $_GET['regulator']; // Corrected variable name

// Include necessary files and establish database connection
include('../config/dbconn.php');

// Fetch data from the tblreports table where ID matches $getid
$stmt = $dbh->prepare("SELECT headline, data FROM tblreport_step3 WHERE id = :id");
$stmt->bindParam(':id', $getid, PDO::PARAM_INT);
$stmt->execute();
$insertedData = $stmt->fetch(PDO::FETCH_ASSOC);

// Create a new PhpWord instance
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

// Define section style for the cover page
$coverPageStyle = array(
  'marginTop' => 1000,
  'marginBottom' => 1000,
  'marginLeft' => 300,
  'marginRight' => 300,
);

// Add a section to the document for the cover page with the cover page style
$coverSection = $phpWord->addSection($coverPageStyle);

// Define the paragraph style without space after
$paragraphStyle = array(
  'alignment' => Jc::CENTER,
  'spaceAfter' => 0, // Remove space after paragraph
);
// Add content to the cover page
$coverSection->addText('ព្រះរាជាណាចក្រកម្ពុជា', array('name' => 'Khmer MEF2', 'size' => 14, 'color' => '#2F5496'), $paragraphStyle);
$coverSection->addText('ជាតិ សាសនា ព្រះមហាក្សត្រ', array('name' => 'Khmer MEF1', 'size' => 14, 'color' => '#2F5496'), $paragraphStyle);

$imgUrl = '../assets/img/icons/brands/logo2.png'; // Path to your image file
// Add a text box below the image on the cover page
$textbox = $coverSection->addTextBox(
  array(
    'width' => 200, // Adjust width as needed
    'height' => 200, // Adjust height as needed
    'alignment' => Jc::LEFT, // Align text box to the left
    'marginLeft' => 300, // Adjust the left margin to move the text box further to the left
    'borderColor' => 'none', // Set border color to none
    'borderSize' => 0, // Set border size to 0 for no outline
  )
);

// Add logo inside the text box
$textbox->addImage(
  $imgUrl,
  array(
    'width' => 100, // Adjust width as needed
    'height' => 100, // Adjust height as needed
    'alignment' => Jc::CENTER // Align image to the center horizontally within the text box
  )
);

// Add text below the logo inside the text box
$textRun = $textbox->addTextRun(array('alignment' => Jc::CENTER));
$textRun->addText('អាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ', array('name' => 'Khmer MEF2', 'size' => 10, 'color' => '#2F5496'));
$textRun->addText("\n"); // Add line break
$textRun->addText('អង្គភាពសវនកម្មផ្ទៃក្នុង', array('name' => 'Khmer MEF2', 'size' => 10, 'color' => '#2F5496'));
$textRun->addText("\n"); // Add line break
$textRun->addText('លេខ:......................អ.ស.ផ.', array('name' => 'Khmer MEF2', 'size' => 10, 'color' => '#2F5496'));

// Add additional text in the middle of the cover page
$additionalTextLines = [
  'របាយការណ៍សវនកម្ម',
  'នៅ' . $getregulator, // Dynamically display the regulator
  'នៃអាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ'
];

// Add space at the top of the cover page
for ($i = 0; $i < 3; $i++) {
  $coverSection->addTextBreak();
}

foreach ($additionalTextLines as $index => $line) {
  $additionalTextRun = $coverSection->addTextRun(array('alignment' => Jc::CENTER));
  $additionalTextRun->addText($line, array('name' => 'Khmer MEF2', 'size' => 22, 'color' => '#2F5496'));
  if ($index !== count($additionalTextLines) - 1) {
    $additionalTextRun->addText("\n", array(), array('spaceAfter' => 0)); // Remove space after text
  }
}
// Function to convert numbers to Khmer numerals
function convertToKhmerNumeric($number)
{
  // Define Khmer numerals
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

  // Convert each digit in the number to Khmer numeral
  $khmerNumber = '';
  $numberArray = str_split($number);
  foreach ($numberArray as $digit) {
    $khmerNumber .= isset($khmerNumerals[$digit]) ? $khmerNumerals[$digit] : $digit;
  }

  return $khmerNumber;
}

// Convert current year to Khmer numeric
$currentYearKhmer = convertToKhmerNumeric(date('Y'));

// Add additional text at the bottom of the cover page with Khmer numeric
$additionalText = "សម្រាប់ឆ្នាំ" . $currentYearKhmer;

// Add additional text to the cover page
$additionalTextRun = $coverSection->addTextRun(array('alignment' => Jc::CENTER, 'marginTop' => 720)); // Adjust marginTop as needed
// Add space at the top of the cover page
for ($i = 0; $i < 15; $i++) {
  $additionalTextRun->addTextBreak(null, 1);
}
// Add the additional text with Khmer numeric
$additionalTextRun->addText($additionalText, array('name' => 'Khmer MEF2', 'size' => 22, 'color' => '#2F5496'), $paragraphStyle);

// Add a new section for the disclaimer
$disclaimerSection = $phpWord->addSection($sectionStyle);

$disclaimerSection->addTextBreak(10); // Add space before the headline
$disclaimerSection->addText('សេចក្តីប្រកាសបដិសេធ', array('name' => 'Khmer MEF2', 'size' => 22, 'color' => '#2F5496'), array('alignment' => Jc::CENTER));

// Add the disclaimer text with justified alignment
$disclaimerText = 'អង្គភាពសវនកម្មផ្ទៃក្នុងនៃអាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ (អ.ស.ហ.) មិនទទួលខុសត្រូវចំពោះទិន្នន័យនិងព័ត៌មានស្តីពីការអនុវត្តការប្រមូលចំណូល ការអនុវត្តចំណាយ ការបង់ភាគទាន និងការប្រើប្រាស់ភាគទាននៅក្នុងរបាយការណ៍ស្ដីពីការពិនិត្យឡើងវិញ ការអនុវត្តការប្រមូលចំណូល ការអនុវត្តចំណាយ ការបង់ភាគទាន និងការប្រើប្រាស់ភាគទាននេះទេ។ អង្គភាពក្រោមឱវាទ អ.ស.ហ. ត្រូវទទួលខុសត្រូវចំពោះភាពពេញលេញ ភាពគ្រប់គ្រាន់ និងភាពត្រឹមត្រូវនៃទិន្នន័យនិងព័ត៌មានស្តីពីការអនុវត្តការប្រមូលចំណូល ការអនុវត្តចំណាយ ការបង់ភាគទាន និងការប្រើប្រាស់ភាគទាននៅក្នុងរបាយការណ៍ស្ដ';

$disclaimerSection->addText($disclaimerText, array('name' => 'Khmer MEF1', 'size' => 12), array('alignment' => Jc::BOTH));
// End of document setup

// Add a section to the document for the TOC
$tocSection = $phpWord->addSection();
// Add a Table of Contents (TOC)
$tocSection->addText('មាតិកា', array('name' => 'Khmer MEF2', 'size' => 12, 'bold' => true), array('alignment' => Jc::CENTER));
$tocSection->addTOC(array('name' => 'Khmer MEF2', 'size' => 12));

// Add a section to the document for the content
$contentSection = $phpWord->addSection($sectionStyle);

// Add the form data to the document
if ($insertedData) {
  $headlines = explode("\n", $insertedData['headline']);
  $data = explode("\n", $insertedData['data']);

  // Iterate through each headline and add it to the document
  foreach ($headlines as $index => $headline) {
    // Clean and decode the headline and data
    $cleanHeadline = preg_replace('/^(&nbsp;|\s)+/', '', htmlspecialchars_decode($headline));
    $cleanData = isset($data[$index]) ? preg_replace('/^(&nbsp;|\s)+/', '', html_entity_decode(strip_tags(trim($data[$index])))) : '';

    // Add the headline as a heading (level 1) with Khmer MEF2 font and justified alignment
    $contentSection->addTitle($cleanHeadline, 1); // Use addTitle method for headings

    // Add the corresponding data, if it exists, with Khmer MEF1 font and justified alignment
    if ($cleanData) {
      $contentSection->addText($cleanData, array('name' => 'Khmer MEF1', 'size' => 12), $contentParagraphStyle);
    }
  }
}

// Set up headers for file download
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="របាយការណ៍សវនកម្ម_' . $getregulator . '.docx"');

// Save the document to output
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('php://output');
?>
