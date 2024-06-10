<?php
// Function to translate text based on user's language preference
if (!function_exists('translate')) {
  function translate($text)
  {
    // Load translations based on user's language preference
    $language = isset($_SESSION['user_language']) ? $_SESSION['user_language'] : 'en';

    // Define the path to the language file
    $languageFile = "../../languages/$language.php";

    // Check if the language file exists
    if (file_exists($languageFile)) {
      // Load translations
      $translations = include($languageFile);
    } else {
      // Fallback to English if the language file does not exist
      $translations = include("../../languages/en.php");
    }

    // Return translated text if available, otherwise return the original text
    return isset($translations[$text]) ? $translations[$text] : $text;
  }
}
