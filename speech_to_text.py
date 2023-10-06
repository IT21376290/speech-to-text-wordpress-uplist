import grpc
import numpy as np
import pyaudio
from riva_api import (
    riva_asr_pb2,
    riva_asr_pb2_grpc,
)

# Define Riva server address
RIVA_SERVER = 'localhost:50051'

def audio_stream():
    # Initialize audio recording
    CHUNK = 1024
    FORMAT = pyaudio.paInt16
    CHANNELS = 1
    RATE = 16000

    p = pyaudio.PyAudio()

    stream = p.open(
        format=FORMAT,
        channels=CHANNELS,
        rate=RATE,
        input=True,
        frames_per_buffer=CHUNK,
    )

    print("Listening...")

    while True:
        yield stream.read(CHUNK)

def transcribe_audio():
    channel = grpc.insecure_channel(RIVA_SERVER)
    stub = riva_asr_pb2_grpc.ASRServiceStub(channel)

    requests = (
        riva_asr_pb2.ASRAudioRequest(audio_chunk=chunk)
        for chunk in audio_stream()
    )

    responses = stub.StreamingRecognize(requests)

    for response in responses:
        print("Transcription:", response.text)

if __name__ == "__main__":
    transcribe_audio()