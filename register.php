<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('books.jpg'); 
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.9; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 10px;
            box-sizing: border-box;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            background-color: rgba(255, 255, 255, 0.95);
            padding-left: 50px;
            padding-right: 50px;
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 25px;
            width: 100%;
            font-size: 24px;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            display: none;
            width: 100%;
            text-align: center;
            font-size: 14px;
        }
        .form-row {
            display: flex;
            width: 100%;
            gap: 50px;
            margin-bottom: 10px;
            margin-right: 20px;
        }
        .form-row div {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .firstname, .lastname, .middlename, .email,
        .username, .password, .confirm_password {
            margin-bottom: 15px;
        }
        label {
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
            border-color: #007bff;
            outline: none;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .toggle-password {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            width: 100%;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .toggle-password input {
            margin-right: 5px;
        }
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 10px;
            }
            form {
                padding: 20px;
            }
            h2 {
                font-size: 20px;
            }
            input[type="submit"] {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <form id="registerForm" action="process_register.php" method="POST" onsubmit="return validateForm()">
        <h2>Register</h2>
        
        <div class="error" id="error"></div>

        <!-- Name fields row -->
        <div class="form-row">
            <div class="firstname">
                <label for="firstname">First Name:</label>
                <input type="text" id="firstname" name="firstname" required>
            </div>
            <div class="middlename">
                <label for="middlename">Middle Name:</label>
                <input type="text" id="middlename" name="middlename">
            </div>
        </div>

        <!-- Last name and email row -->
        <div class="form-row">
            <div class="lastname">
                <label for="lastname">Last Name:</label>
                <input type="text" id="lastname" name="lastname" required>
            </div>
            <div class="email">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
        </div>

        <!-- Username and password fields row -->
        <div class="form-row">
            <div class="username">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="password">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
        </div>

        <!-- Confirm password field -->
        <div class="confirm_password">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <div class="toggle-password">
            <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()"> Show Passwords
        </div>

        <input type="submit" value="Register">
    </form>

    <script>
        function validateForm() {
            const firstname = document.getElementById('firstname').value.trim();
            const lastname = document.getElementById('lastname').value.trim();
            const email = document.getElementById('email').value.trim();
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const errorDiv = document.getElementById('error');

            errorDiv.style.display = 'none';
            errorDiv.textContent = '';

            if (!firstname || !lastname || !email || !username) {
                errorDiv.textContent = 'All fields are required except Middle Name.';
                errorDiv.style.display = 'block';
                return false;
            }

            if (!/^\S+@\S+\.\S+$/.test(email)) {
                errorDiv.textContent = 'Please enter a valid email address.';
                errorDiv.style.display = 'block';
                return false;
            }

            if (username.length < 5 || !/^[a-zA-Z]+$/.test(username)) {
                errorDiv.textContent = 'Username must be at least 5 characters long and contain only characters, no numbers.';
                errorDiv.style.display = 'block';
                return false;
            }

            if (password.length < 8) {
                errorDiv.textContent = 'Password must be at least 8 characters long.';
                errorDiv.style.display = 'block';
                return false;
            }

            if (!/(?=.*[a-zA-Z])(?=.*[0-9])/.test(password)) {
                errorDiv.textContent = 'Password must contain at least one letter and one number.';
                errorDiv.style.display = 'block';
                return false;
            }

            if (password !== confirmPassword) {
                errorDiv.textContent = 'Passwords do not match.';
                errorDiv.style.display = 'block';
                return false;
            }

            return true; 
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const showPasswordCheckbox = document.getElementById('showPassword');
            const inputType = showPasswordCheckbox.checked ? 'text' : 'password';
            passwordInput.type = inputType;
            confirmPasswordInput.type = inputType;
        }
    </script>
</body>
</html>
