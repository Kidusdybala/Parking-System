from flask import Flask, request, jsonify
import mysql.connector
from collections import Counter

app = Flask(__name__)

# Connect to your XAMPP MySQL database
def get_db_connection():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="rms"
    )

@app.route('/recommend', methods=['POST'])
def recommend():
    user_id = request.json.get('user_id')
    conn = get_db_connection()
    cursor = conn.cursor()

    # Fetch user’s past reserved parking spots
    cursor.execute("SELECT parking_spot_id FROM reservations WHERE user_id = %s", (user_id,))
    results = cursor.fetchall()
    conn.close()

    if not results:
        return jsonify({'recommended_spot': None})

    spot_ids = [row[0] for row in results]
    most_common = Counter(spot_ids).most_common(1)[0][0]

    return jsonify({'recommended_spot': most_common})

if __name__ == '__main__':
    app.run(debug=True, port=5000)
