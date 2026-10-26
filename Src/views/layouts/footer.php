<?php
use SmartKasir\Utils\SessionManager;
$isLoggedIn = SessionManager::isLoggedIn();
?>
<?php if ($isLoggedIn): ?>
<footer class="app-footer">
    <div><strong>SmartKasir POS</strong> &copy; 2026 - Tugas Besar Rekayasa Perangkat Lunak / IMPAL Telkom University Surabaya</div>
</footer>
<?php endif; ?>

<script>
// Toggle Password Visibility
function togglePasswordVisibility(fieldId, iconId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);
    if (!field || !icon) return;

    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Quick Auto-fill for Testing Demo Credentials
function fillDemo(username, password) {
    const userInput = document.getElementById('username');
    const passInput = document.getElementById('password');
    if (userInput && passInput) {
        userInput.value = username;
        passInput.value = password;
        userInput.focus();
    }
}
</script>
</body>
</html>
