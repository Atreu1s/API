<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Подключение к базе данных
$servername = "localhost";
$username = "root";  // Измените на ваш логин
$password = "";      // Измените на ваш пароль
$dbname = "task_manager";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['message' => 'Database connection failed: ' . $conn->connect_error]));
}

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));

switch ($method) {
    case 'GET':
        if (isset($request[0]) && is_numeric($request[0])) {
            // Получение одной задачи по ID
            $id = intval($request[0]);
            $result = $conn->query("SELECT * FROM tasks WHERE id = $id");
            $task = $result->fetch_assoc();
            if ($task) {
                echo json_encode($task);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Task not found']);
            }
        } else {
            // Получение всех задач
            $result = $conn->query("SELECT * FROM tasks");
            $tasks = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($tasks);
        }
        break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['title'])) {
                $title = $conn->real_escape_string($data['title']);
                $conn->query("INSERT INTO tasks (title) VALUES ('$title')");
                $id = $conn->insert_id;
                echo json_encode(['id' => $id, 'title' => $title, 'completed' => 0]);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Bad Request']);
            }
            break;
        

            case 'PUT':
                if (isset($request[1]) && is_numeric($request[1])) {
                    $id = intval($request[1]);
                    $data = json_decode(file_get_contents('php://input'), true);
        
                    // Проверка наличия необходимых данных
                    if (isset($data['title']) && isset($data['completed'])) {
                        $title = $conn->real_escape_string($data['title']);
                        $completed = intval($data['completed']); // Преобразуем в целое число
                        
                        // Обновление задачи в базе данных
                        $sql = "UPDATE tasks SET title = '$title', completed = $completed WHERE id = $id";
                        if ($conn->query($sql) === TRUE) {
                            if ($conn->affected_rows > 0) {
                                echo json_encode(['message' => 'Task updated successfully', 'id' => $id, 'title' => $title, 'completed' => $completed]);
                            } else {
                                echo json_encode(['message' => 'No task found with the provided ID or no changes made']);
                            }
                        } else {
                            http_response_code(500);
                            echo json_encode(['message' => 'Failed to update task']);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(['message' => 'Bad Request: Missing title or completed status']);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Bad Request: Invalid task ID']);
                }
                break;
            
            

                case 'DELETE':
                    if (isset($request[1]) && is_numeric($request[1])) {
                        $id = intval($request[1]);
                        
                        // Удаление задачи из базы данных
                        $sql = "DELETE FROM tasks WHERE id = $id";
                        if ($conn->query($sql) === TRUE) {
                            if ($conn->affected_rows > 0) {
                                echo json_encode(['message' => 'Task deleted successfully']);
                            } else {
                                http_response_code(404);
                                echo json_encode(['message' => 'Task not found']);
                            }
                        } else {
                            http_response_code(500);
                            echo json_encode(['message' => 'Failed to delete task']);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(['message' => 'Bad Request: Invalid task ID']);
                    }
                    break;
            

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
        break;
}

$conn->close();
?>
