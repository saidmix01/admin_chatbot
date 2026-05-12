<body style="background: linear-gradient(135deg, #f9fafb 0%, #eef2ff 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; font-family: 'Inter', sans-serif;">

    <div style="background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(99, 102, 241, 0.12); padding: 2.5rem; width: 100%; max-width: 400px; margin: 1rem;">
        
        <div style="text-align: center; margin-bottom: 2rem;">
            <img src="<?=base_url()?>assets/img/logo-wapi.svg" alt="Wapi" style="height: 36px;">
            <h4 style="margin-top: 1rem; font-size: 1.25rem; font-weight: 700; color: #111827;">Bienvenido</h4>
            <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Inicia sesión en tu panel de administración</p>
        </div>

        <form id="form_login">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Email</label>
                <input type="email" name="us_email" placeholder="admin@ejemplo.com"
                    style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 0.875rem; outline: none; box-sizing: border-box; transition: border-color 0.15s, box-shadow 0.15s;"
                    onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'"
                    onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Contraseña</label>
                <input type="password" name="us_password" placeholder="••••••••"
                    style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 0.875rem; outline: none; box-sizing: border-box; transition: border-color 0.15s, box-shadow 0.15s;"
                    onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'"
                    onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
            </div>

            <button type="submit" onclick="login('form_login');"
                style="width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; border: none; border-radius: 10px; font-size: 0.9375rem; font-weight: 600; cursor: pointer; transition: opacity 0.15s;"
                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                Iniciar sesión
            </button>

            <p style="text-align: center; margin-top: 1.5rem; font-size: 0.75rem; color: #9ca3af;">
                Plataforma WhatsApp para negocios
            </p>
        </form>

    </div>

</body>
