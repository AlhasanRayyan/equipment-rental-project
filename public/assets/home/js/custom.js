//    reset Password 
   const pwd = document.getElementById('passwordInput');
    const toggle = document.getElementById('togglePassword');

    toggle.addEventListener('click', () => {
      const isPassword = pwd.type === 'password';
      pwd.type = isPassword ? 'text' : 'password';

      // بدّل الأيقونة بين fa-eye و fa-eye-slash
      toggle.classList.toggle('fa-eye');
      toggle.classList.toggle('fa-eye-slash');
    });