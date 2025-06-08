<?php
require_once "config.php";
 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'search') {
    header('Content-Type: application/json');
    $courses = new CoursesBackend();
    $search_results = $courses->searchCourses($_POST['search_term']);
    echo json_encode($search_results);
    exit;
}

class CoursesBackend {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }


    public function getCourses($category_id = null) {
        try {
            if ($category_id) {
                $stmt = $this->conn->prepare("
                    SELECT c.*, u.name as tutor_name, u.profile_image, cat.categore_name
                    FROM courses c 
                    JOIN users u ON c.user_id = u.id 
                    LEFT JOIN categore cat ON c.categore_id = cat.categore_id
                    WHERE c.categore_id = ?
                ");
                $stmt->execute([$category_id]);
            } else {
                $stmt = $this->conn->prepare("
                    SELECT c.*, u.name as tutor_name, u.profile_image, cat.categore_name
                    FROM courses c 
                    JOIN users u ON c.user_id = u.id 
                    LEFT JOIN categore cat ON c.categore_id = cat.categore_id
                ");
                $stmt->execute();
            }
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getCourses: " . $e->getMessage());
            return [];
        }
    }

    // Get all categories
    public function getCategories() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM categore");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getCategories: " . $e->getMessage());
            return [];
        }
    }

    // Get course details by ID
    public function getCourseById($course_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT c.*, u.name as tutor_name, u.profile_image, cat.categore_name
                FROM courses c 
                JOIN users u ON c.user_id = u.id 
                LEFT JOIN categore cat ON c.categore_id = cat.categore_id
                WHERE c.course_id = ?
            ");
            $stmt->execute([$course_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error in getCourseById: " . $e->getMessage());
            return null;
        }
    }

    // Add new course
    public function addCourse($data) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO courses (user_id, course_name, description, price, image_url, categore_id)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $data['user_id'],
                $data['course_name'],
                $data['description'],
                $data['price'],
                $data['image_url'],
                $data['categore_id']
            ]);
        } catch (PDOException $e) {
            error_log("Error in addCourse: " . $e->getMessage());
            return false;
        }
    }

    // Update course
    public function updateCourse($course_id, $data) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE courses 
                SET course_name = ?, description = ?, price = ?, image_url = ?, categore_id = ?
                WHERE course_id = ?
            ");
            return $stmt->execute([
                $data['course_name'],
                $data['description'],
                $data['price'],
                $data['image_url'],
                $data['categore_id'],
                $course_id
            ]);
        } catch (PDOException $e) {
            error_log("Error in updateCourse: " . $e->getMessage());
            return false;
        }
    }

    // Delete course
    public function deleteCourse($course_id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM courses WHERE course_id = ?");
            return $stmt->execute([$course_id]);
        } catch (PDOException $e) {
            error_log("Error in deleteCourse: " . $e->getMessage());
            return false;
        }
    }

    // Search courses
    public function searchCourses($search_term) {
        try {
            $search_term = '%' . $search_term . '%';
            $stmt = $this->conn->prepare("
                SELECT c.*, u.name as tutor_name, u.profile_image, cat.categore_name 
                FROM courses c 
                LEFT JOIN users u ON c.user_id = u.id 
                LEFT JOIN categore cat ON c.categore_id = cat.categore_id 
                WHERE c.course_name LIKE ? 
                OR c.describtion LIKE ? 
                OR cat.categore_name LIKE ?
                ORDER BY c.course_name ASC
            ");
            $stmt->execute([$search_term, $search_term, $search_term]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Search results for term '$search_term': " . print_r($results, true));
            return $results;
        } catch(PDOException $e) {
            error_log("Error in searchCourses: " . $e->getMessage());
            return [];
        }
    }

    // Get courses by tutor
    public function getCoursesByTutor($user_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT c.*, u.name as tutor_name, u.profile_image, cat.categore_name
                FROM courses c 
                JOIN users u ON c.user_id = u.id 
                LEFT JOIN categore cat ON c.categore_id = cat.categore_id
                WHERE c.user_id = ?
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getCoursesByTutor: " . $e->getMessage());
            return [];
        }
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courses = new CoursesBackend();
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'search':
            $search_term = $_POST['search_term'] ?? '';
            $results = $courses->searchCourses($search_term);
            echo json_encode($results);
            break;

        case 'get_courses':
            $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
            $results = $courses->getCourses($category_id);
            echo json_encode($results);
            break;

        case 'get_categories':
            echo json_encode($courses->getCategories());
            break;

        case 'get_course':
            $course_id = (int)$_POST['course_id'];
            echo json_encode($courses->getCourseById($course_id));
            break;

        case 'add_course':
            $data = [
                'user_id' => $_POST['user_id'],
                'course_name' => $_POST['course_name'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'image_url' => $_POST['image_url'],
                'categore_id' => $_POST['categore_id']
            ];
            $result = $courses->addCourse($data);
            echo json_encode(['success' => $result]);
            break;

        case 'update_course':
            $course_id = $_POST['course_id'];
            $data = [
                'course_name' => $_POST['course_name'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'image_url' => $_POST['image_url'],
                'categore_id' => $_POST['categore_id']
            ];
            $result = $courses->updateCourse($course_id, $data);
            echo json_encode(['success' => $result]);
            break;

        case 'delete_course':
            $course_id = $_POST['course_id'];
            $result = $courses->deleteCourse($course_id);
            echo json_encode(['success' => $result]);
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
    }
}
?> 