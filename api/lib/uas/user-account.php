<?php
use Firebase\JWT\JWT;
use PHPMailer\PHPMailer\PHPMailer;

class UserAccount {
	private $db;
	private $dbHost;
	private $dbUsername;
	private $dbPassword;
	private $dbName;
	private $emailHost;
	private $emailPort;
	private $emailOwner;
	private $emailAddress;
	private $emailPassword;
	private $emailSmtpAuth;
	private $emailSmtpSecure;
	private $apiRootLink;
	private $authTokenPrivateKey;

	public function __construct($env) {
		$this->dbHost = $env['DB_HOST'];
		$this->dbUsername = $env['DB_USER'];
		$this->dbPassword = $env['DB_PASS'];
		$this->dbName = $env['DB_NAME'];
		$this->emailHost = $env['EMAIL_HOST'];
		$this->emailPort = $env['EMAIL_PORT'];
		$this->emailOwner = $env['EMAIL_NAME'];
		$this->emailAddress = $env['EMAIL_USER'];
		$this->emailPassword = $env['EMAIL_PASS'];
		$this->emailSmtpAuth = $env['EMAIL_SMTP_AUTH'];
		$this->emailSmtpSecure = $env['EMAIL_SMTP_SECURE'];
		$this->apiRootLink = $env['API_ROOT_LINK'];
		$this->authTokenPrivateKey = $env['AUTH_TOKEN_PRIVATE_KEY'];
	}

	private function connectDB() {
		// Connect to database
		$this->db = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
		// Check connection
		if ($this->db->connect_error) {
			die("Connection failed: " . $this->db->connect_error);
		}
	}

	private function disconnectDB() {
		// Closing the connection
		$res = $this->db->close();
		// Report error if connection doesn't close
		if (!$res) {
			print("Disconnect error: There is an issue while closing the connection with DB.");
		}
	}

	private function validateUsername($username) {
		if (empty($username)) {
			// Username is required
			http_response_code(400);
			echo json_encode(['message' => 'Username is required']);
			exit;
		}
		if (strlen($username) < 3 || strlen($username) > 20) {
			// Username must be between 3 and 20 characters long
			http_response_code(400);
			echo json_encode(['message' => 'Username must be between 3 and 20 characters long']);
			exit;
		}
	}

	private function validatePassword($password) {
		if (empty($password)) {
			// Password is required
			http_response_code(400);
			echo json_encode(['message' => 'Password is required']);
			exit;
		}
		if (strlen($password) < 8) {
			// Password must be at least 8 characters long
			http_response_code(400);
			echo json_encode(['message' => 'Password must be at least 8 characters long']);
			exit;
		}
	}

	private function validateEmail($email) {
		if (empty($email)) {
			// Email is required
			http_response_code(400);
			echo json_encode(['message' => 'Email is required']);
			exit;
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			// Email must be a valid email address
			http_response_code(400);
			echo json_encode(['message' => 'Email must be a valid email address']);
			exit;
		}
	}

	private function verifyAccount($username, $email) {
		// Check if user already exists
		$query = 'SELECT * FROM users WHERE username = ? OR email = ?';
		$stmt = $this->db->prepare($query);
		$stmt->bind_param('ss', $username, $email);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			// User already exists
			http_response_code(400);
			echo json_encode(['message' => 'User already exists']);
			$this->disconnectDB();
			exit;
		}
	}

	private function createNewUser($username, $hashed_password, $email) {
		// Insert user into database
		$query = 'INSERT INTO users (username, password, email) VALUES (?, ?, ?)';
		$stmt = $this->db->prepare($query);
		$stmt->bind_param('sss', $username, $hashed_password, $email);
		$stmt->execute();
	}

	public function register() {
		// Get user data from request body
		$request = Flight::request();
		$username = $request->data['username'];
		$password = $request->data['password'];
		$email = $request->data['email'];
		// Validate user data
		$this->validateUsername($username);
		$this->validatePassword($password);
		$this->validateEmail($email);
		// Check if user already exists
		$this->connectDB();
		$this->verifyAccount($username, $email);
		// Hash password
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		// Insert user into database
		$this->createNewUser($username, $hashed_password, $email);
		$this->disconnectDB();
		http_response_code(201);
		echo json_encode(['message' => 'User created']);
	}

	private function verifyCredential($username, $password) {
		// Check if user exists and password is correct
		$query = 'SELECT * FROM users WHERE username = ?';
		$stmt = $this->db->prepare($query);
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows == 0) {
			// User does not exist
			http_response_code(400);
			echo json_encode(['message' => 'Invalid username or password']);
			$this->disconnectDB();
			exit;
		}
		$user = $result->fetch_assoc();
		if (!password_verify($password, $user['password'])) {
			// Password is incorrect
			http_response_code(400);
			echo json_encode(['message' => 'Invalid username or password']);
			$this->disconnectDB();
			exit;
		}
		return $user;
	}

	private function getAuthToken($user) {
		// User exists and password is correct
		$key = $this->authTokenPrivateKey;
		$payload = [
			'iss' => $this->apiRootLink,
			'aud' => $this->apiRootLink,
			'iat' => time(),
			'exp' => time() + (60 * 60), // 1 hour
			'sub' => $user['id']
		];
		$token = JWT::encode($payload, $key, 'RS256');
		return $token;
	}

	public function login() {
		// Get user data from request body
		$request = Flight::request();
		$username = $request->data['username'];
		$password = $request->data['password'];
		// Validate user data
		$this->validateUsername($username);
		$this->validatePassword($password);
		// Check if user exists and password is correct
		$this->connectDB();
		$user = $this->verifyCredential($username, $password);
		// Generate and return auth token
		$token = $this->getAuthToken($user);
		$this->disconnectDB();
		http_response_code(200);
		echo json_encode(['message' => 'User signed in', 'token' => $token]);
	}

	private function verifyEmail($email) {
		$query = 'SELECT * FROM users WHERE email = ?';
		$stmt = $this->db->prepare($query);
		$stmt->bind_param('s', $email);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows == 0) {
			// User does not exist
			http_response_code(400);
			echo json_encode(['message' => 'User does not exist']);
			exit;
		}
		return $result->fetch_assoc();
	}

	private function storeToken($user, $token) {
		// Store token in database
		$query = 'INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))';
		$stmt = $this->db->prepare($query);
		$stmt->bind_param('is', $user['id'], $token);
		$stmt->execute();
	}

	private function sendEmail($email, $token) {
		// Send token to user's email
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->Host = $this->emailHost;
		$mail->SMTPAuth = $this->emailSmtpAuth;
		$mail->Username = $this->emailAddress;
		$mail->Password = $this->emailPassword;
		$mail->SMTPSecure = $this->emailSmtpSecure;
		$mail->Port = $this->emailPort;

		$mail->setFrom($this->emailAddress, $this->emailOwner);
		$mail->addAddress($email);
		$mail->isHTML(true);
		$mail->Subject = 'Password Reset';
		$mail->Body = '<p>Please click the link below to reset your password:</p><p><a href="' . $this->apiRootLink . '/reset-password?token=' . $token . '">' . $this->apiRootLink . '/reset-password?token=' . $token . '</a></p>';
		$mail->AltBody = 'Please open the given link in the browser to reset your password: ' . $this->apiRootLink . '/reset-password?token=' . $token;

		if (!$mail->send()) {
			http_response_code(500);
			echo json_encode(['message' => 'Failed to send password reset email']);
			exit;
		}
	}

	public function forgotPassword() {
		// Get user data from request body
		$request = Flight::request();
		$email = $request->data['email'];
		// Validate user data
		$this->validateEmail($email);
		// Check if user exists
		$this->connectDB();
		$user = $this->verifyEmail($email);
		// Generate password reset token
		$token = bin2hex(random_bytes(16));
		// Store token in database
		$this->storeToken($user, $token);
		$this->disconnectDB();
		// Send password reset token to user's email
		$this->sendEmail($email, $token);
		http_response_code(200);
		echo json_encode(['message' => 'Password reset token sent']);
	}
}