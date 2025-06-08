from flask import Flask, request, jsonify
import google.generativeai as genai
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

# Configure Gemini
genai.configure(api_key="AIzaSyB6-AvwznImn5aGgTgmNCRsIe3xMgAs2ek")  # Replace with your actual API key
model = genai.GenerativeModel('gemini-2.0-flash')
chat = model.start_chat(history=[])


@app.route('/chat', methods=['POST'])
def handle_chat():
    user_message = request.json.get('message')
    if not user_message:
        return jsonify({"error": "No message provided"}), 400

    try:
        response = chat.send_message(user_message)
        return jsonify({
            "response": response.text,
            "status": "success"
        })
    except Exception as e:
        return jsonify({
            "error": str(e),
            "status": "error"
        }), 500


if __name__ == '__main__':
    app.run(debug=True)