<?php
/*
Plugin Name: Listings Search by Voice | Uplist.lk
Description: This plugin allows users to enter search keywords by voice.
Version: 1.0
*/

function speech_to_text_form() {
    // Display the audio recording form
    $output = '<div id="speech-to-text-form">';
    $output .= '<button id="start-recording">Tell What You Want To Search</button>';
    $output .= '<button id="stop-recording" style="display:none;">Stop Recording</button>';
    $output .= '<div id="transcription-result"></div>';
    $output .= '</div>';
    return $output;
}

function speech_to_text_script() {
    // Enqueue JavaScript file for handling audio recording and transcription
    wp_enqueue_script('speech-to-text-js', plugin_dir_url(__FILE__) . 'speech_to_text.js', array('jquery'));
    wp_localize_script('speech-to-text-js', 'speech_to_text_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('speech-to-text-security'),
    ));
}

add_shortcode('speech_to_text', 'speech_to_text_form');
add_action('wp_enqueue_scripts', 'speech_to_text_script');

function transcribe_audio() {
    check_ajax_referer('speech-to-text-security', 'security');

    if (isset($_FILES['audio_data'])) {
        $audio_blob = file_get_contents($_FILES['audio_data']['tmp_name']);
        
        // Send the audio blob to your Riva-based application for transcription
        // Process the response and return the transcription

        // Example Riva API usage:
        $transcription = call_riva_transcription_api($audio_blob);
        
        // Return a sample response for testing
        $transcription = "This is a sample transcription.";

        echo $transcription;
    }

    wp_die();
}

add_action('wp_ajax_transcribe_audio', 'transcribe_audio');
add_action('wp_ajax_nopriv_transcribe_audio', 'transcribe_audio');


?>
