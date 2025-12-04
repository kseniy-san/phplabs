"""
Простое веб-приложение для выполнения SQL-запросов к базе данных тур-агентства
"""
from flask import Flask, render_template, request, jsonify
import mysql.connector
from mysql.connector import Error
import json
import os

app = Flask(__name__)

# Конфигурация подключения к вашей базе данных из Workbench
# ИЗМЕНИТЕ ЭТИ ДАННЫЕ ПОД ВАШУ КОНФИГУРАЦИЮ!
DB_CONFIG = {
    'host': 'localhost',        # Обычно localhost HP-NB-KSN Local instance MySQL80
    'port': 3307,               # Стандартный порт MySQL
    'database': 'Travel_agency', # Имя вашей базы данных travel_agency
    'user': 'root',             # Ваш пользователь MySQL (обычно root)
    'password': '45_P67'      # Ваш пароль от MySQL
}

def get_db_connection():
    """Создание подключения к базе данных"""
    try:
        connection = mysql.connector.connect(
            host=DB_CONFIG['host'],
            port=DB_CONFIG['port'],
            database=DB_CONFIG['database'],
            user=DB_CONFIG['user'],
            password=DB_CONFIG['password'],
            charset='utf8mb4',
            collation='utf8mb4_unicode_ci'
        )
        return connection, None
    except Error as e:
        return None, str(e)

def execute_query(sql_query):
    """Выполнение SQL-запроса"""
    connection, error = get_db_connection()
    
    if error:
        return None, error, 0
    
    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)
        cursor.execute(sql_query)
        
        # Определяем тип запроса
        query_type = sql_query.strip().lower().split()[0]
        
        if query_type in ('select', 'show', 'describe', 'explain'):
            # Запросы, которые возвращают данные
            results = cursor.fetchall()
            columns = cursor.column_names
            rowcount = len(results)
            
            # Преобразуем данные для JSON
            data = []
            for row in results:
                # Преобразуем все значения в строки
                row_dict = {}
                for key, value in row.items():
                    if value is None:
                        row_dict[key] = 'NULL'
                    else:
                        row_dict[key] = str(value)
                data.append(row_dict)
                
            return {'data': data, 'columns': columns}, None, rowcount
        else:
            # Запросы, которые не возвращают данные
            connection.commit()
            rowcount = cursor.rowcount
            return None, None, rowcount
            
    except Error as e:
        return None, str(e), 0
    finally:
        if cursor:
            cursor.close()
        if connection and connection.is_connected():
            connection.close()

@app.route('/')
def index():
    """Главная страница с формой для ввода SQL"""
    return render_template('index.html')

@app.route('/execute', methods=['POST'])
def execute_sql():
    """Обработка SQL-запроса"""
    try:
        # Получаем SQL-запрос из формы
        sql_query = request.form.get('sql_query', '').strip()
        
        if not sql_query:
            return jsonify({
                'success': False,
                'error': 'Введите SQL-запрос'
            })
        
        # Выполняем запрос
        result, error, rowcount = execute_query(sql_query)
        
        if error:
            return jsonify({
                'success': False,
                'error': error
            })
        
        if result:
            # Запрос вернул данные
            return jsonify({
                'success': True,
                'has_data': True,
                'data': result['data'],
                'columns': result['columns'],
                'rowcount': rowcount
            })
        else:
            # Запрос не вернул данные (UPDATE, DELETE, INSERT и т.д.)
            return jsonify({
                'success': True,
                'has_data': False,
                'message': f'Запрос выполнен успешно. Обработано строк: {rowcount}',
                'rowcount': rowcount
            })
            
    except Exception as e:
        return jsonify({
            'success': False,
            'error': f'Ошибка при выполнении запроса: {str(e)}'
        })

@app.route('/test-connection')
def test_connection():
    """Тестирование подключения к базе данных"""
    connection, error = get_db_connection()
    
    if error:
        return jsonify({
            'success': False,
            'error': f'Ошибка подключения: {error}',
            'config': DB_CONFIG
        })
    
    try:
        cursor = connection.cursor()
        cursor.execute("SELECT DATABASE() as db, USER() as user")
        result = cursor.fetchone()
        cursor.close()
        connection.close()
        
        return jsonify({
            'success': True,
            'message': f'Успешное подключение к базе данных!',
            'database': result[0],
            'user': result[1],
            'config': DB_CONFIG
        })
    except Error as e:
        return jsonify({
            'success': False,
            'error': f'Ошибка при тестировании: {str(e)}'
        })

@app.route('/quick-queries')
def quick_queries():
    """Предопределенные запросы для быстрого доступа"""
    queries = [
        {
            'name': 'Все туры',
            'sql': 'SELECT idтура, название, страна, стоимость, длительность FROM туры ORDER BY страна'
        },
        {
            'name': 'Туры по России',
            'sql': "SELECT название, маршрут, стоимость FROM туры WHERE страна = 'Россия' ORDER BY стоимость"
        },
        {
            'name': 'Все туристы',
            'sql': 'SELECT idтуриста, фамилия, имя, отчество, город, телефон FROM туристы ORDER BY фамилия'
        },
        {
            'name': 'Неоплаченные путёвки',
            'sql': """
                SELECT p.idпутёвка, t.название, tr.фамилия, tr.имя, p.статус_оплаты, p.дата_отправления
                FROM путёвка p
                JOIN тур_сезон ts ON p.idтур_сезон = ts.idтур_сезон
                JOIN туры t ON ts.idтура = t.идтура
                JOIN туристы tr ON p.idтуриста = tr.idтуриста
                WHERE p.статус_оплаты IN ('ожидание', 'частично')
                ORDER BY p.дата_отправления
            """
        }
    ]
    
    return jsonify({'queries': queries})

if __name__ == '__main__':
    # Запуск приложения
    print("=" * 60)
    print("ВЕБ-ПРИЛОЖЕНИЕ ДЛЯ РАБОТЫ С БАЗОЙ ДАННЫХ ТУР-АГЕНТСТВА")
    print("=" * 60)
    print(f"Конфигурация подключения:")
    print(f"  Хост: {DB_CONFIG['host']}")
    print(f"  Порт: {DB_CONFIG['port']}")
    print(f"  База данных: {DB_CONFIG['database']}")
    print(f"  Пользователь: {DB_CONFIG['user']}")
    print("=" * 60)
    print("Откройте браузер и перейдите по адресу: http://localhost:5000")
    print("=" * 60)
    
    app.run(debug=True, host='0.0.0.0', port=5000)