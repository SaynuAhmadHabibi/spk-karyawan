<?php
/**
 * Router - Application routing helper
 * 
 * This class handles routing of requests to appropriate controllers.
 * It centralizes routing logic and makes it easier to manage.
 * 
 * @author Development Team
 * @version 1.0
 */

class Router {
    private array $routes = [];
    private \PDO $pdo;
    private string $currentAction;
    private string $currentSub;
    private ?int $currentId;
    
    /**
     * Constructor
     * 
     * @param \PDO $pdo Database connection
     */
    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
        $this->currentAction = $_GET['act'] ?? 'login';
        $this->currentSub = $_GET['sub'] ?? '';
        $this->currentId = isset($_GET['id']) ? (int)$_GET['id'] : null;
    }
    
    /**
     * Get current action
     * 
     * @return string Current action name
     */
    public function getAction(): string {
        return $this->currentAction;
    }
    
    /**
     * Get current sub-action
     * 
     * @return string Current sub-action name
     */
    public function getSub(): string {
        return $this->currentSub;
    }
    
    /**
     * Get current resource ID
     * 
     * @return int|null Resource ID or null
     */
    public function getId(): ?int {
        return $this->currentId;
    }
    
    /**
     * Register a route
     * 
     * @param string $action Action name
     * @param string $controller Controller class name
     * @param array $methods Allowed HTTP methods
     * @return void
     */
    public function register(string $action, string $controller, array $methods = []): void {
        $this->routes[$action] = [
            'controller' => $controller,
            'methods' => $methods ?: [METHOD_GET, METHOD_POST]
        ];
    }
    
    /**
     * Check if action exists in routes
     * 
     * @param string $action Action name to check
     * @return bool True if action is registered
     */
    public function hasRoute(string $action): bool {
        return isset($this->routes[$action]);
    }
    
    /**
     * Get controller class name for action
     * 
     * @param string $action Action name
     * @return string|null Controller class name or null
     */
    public function getController(string $action): ?string {
        return $this->routes[$action]['controller'] ?? null;
    }
    
    /**
     * Check if HTTP method is allowed for action
     * 
     * @param string $action Action name
     * @param string $method HTTP method
     * @return bool True if method is allowed
     */
    public function isMethodAllowed(string $action, string $method = ''): bool {
        if (!$this->hasRoute($action)) {
            return false;
        }
        
        $method = $method ?: $_SERVER['REQUEST_METHOD'];
        $allowedMethods = $this->routes[$action]['methods'];
        
        return in_array($method, $allowedMethods);
    }
    
    /**
     * Handle routing and controller dispatch
     * 
     * Automatically includes the controller file and instantiates it,
     * then calls the appropriate method based on sub-action.
     * 
     * @param callable|null $onNotFound Callback if route not found
     * @return void
     * @throws Exception
     */
    public function dispatch(callable $onNotFound = null): void {
        $action = $this->currentAction;
        $sub = $this->currentSub;
        
        // Check if route exists
        if (!$this->hasRoute($action)) {
            if ($onNotFound) {
                call_user_func($onNotFound, $action);
            }
            return;
        }
        
        // Load and instantiate controller
        $controllerClass = $this->getController($action);
        $controllerFile = __DIR__ . '/../controllers/' . $controllerClass . '.php';
        
        if (!file_exists($controllerFile)) {
            throw new Exception("Controller file not found: $controllerFile");
        }
        
        require_once $controllerFile;
        
        if (!class_exists($controllerClass)) {
            throw new Exception("Controller class not found: $controllerClass");
        }
        
        $controller = new $controllerClass($this->pdo);
        
        // Call method based on sub-action
        if ($sub) {
            $method = $sub;
            if (method_exists($controller, $method)) {
                if ($this->currentId) {
                    $controller->$method($this->currentId);
                } else {
                    $controller->$method();
                }
                return;
            }
        }
        
        // Default to index method
        if (method_exists($controller, 'index')) {
            $controller->index();
        }
    }
    
    /**
     * Generate URL for action
     * 
     * @param string $action Action name
     * @param string|null $sub Sub-action (optional)
     * @param int|null $id Resource ID (optional)
     * @return string Generated URL
     */
    public static function url(string $action, ?string $sub = null, ?int $id = null): string {
        $url = "index.php?act=$action";
        
        if ($sub) {
            $url .= "&sub=$sub";
        }
        
        if ($id) {
            $url .= "&id=$id";
        }
        
        return $url;
    }
    
    /**
     * Generate edit URL for resource
     * 
     * @param string $action Action name
     * @param int $id Resource ID
     * @return string Edit URL
     */
    public static function editUrl(string $action, int $id): string {
        return self::url($action, 'edit', $id);
    }
    
    /**
     * Generate delete URL for resource
     * 
     * @param string $action Action name
     * @param int $id Resource ID
     * @return string Delete URL
     */
    public static function deleteUrl(string $action, int $id): string {
        return self::url($action, 'delete', $id);
    }
    
    /**
     * Generate create URL
     * 
     * @param string $action Action name
     * @return string Create URL
     */
    public static function createUrl(string $action): string {
        return self::url($action, 'create');
    }
}

?>
