jQuery(document).ready(function($) {
    var audioChunks = [];
    var isRecording = false;
    var mediaRecorder;

    $('#start-recording').on('click', function() {
        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(function(stream) {
                mediaRecorder = new MediaRecorder(stream);

                mediaRecorder.ondataavailable = function(event) {
                    audioChunks.push(event.data);
                };

                mediaRecorder.onstop = function() {
                    if (isRecording) {
                        var audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                        var formData = new FormData();
                        formData.append('audio', audioBlob);

                        $.ajax({
                            type: 'POST',
                            url: speech_to_text_vars.ajax_url,
                            data: {
                                action: 'transcribe_audio',
                                security: speech_to_text_vars.security,
                                audio_data: formData,
                            },
                            success: function(response) {
                                $('#transcription-result').html(response);
                            }
                        });

                        audioChunks = [];
                        isRecording = false;
                        $('#start-recording').show();
                        $('#stop-recording').hide();
                    }
                };

                mediaRecorder.start();
                isRecording = true;
                $('#start-recording').hide();
                $('#stop-recording').show();
            });
    });

    $('#stop-recording').on('click', function() {
        if (isRecording) {
            mediaRecorder.stop();
        }
    });
});
