import { useState } from "react";
import { useNavigate, Link } from "react-router-dom";
import { authFetch } from "../pages/admin/utils/api";

export default function Login({ setUsuario }) {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [recoverMode, setRecoverMode] = useState(false);
  const [recoverEmail, setRecoverEmail] = useState("");
  const [recoverMsg, setRecoverMsg] = useState("");
  const navigate = useNavigate();

  // 🔹 Login normal
  const handleLogin = async (e) => {
    e.preventDefault();
    setError("");

    try {
      const res = await fetch("http://127.0.0.1:8000/api/users/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });

      const data = await res.json();

      if (!res.ok) {
        setError(data.error || "Error al iniciar sesión");
        return;
      }

      setUsuario(data.usuario);
      localStorage.setItem("usuario", JSON.stringify(data.usuario));

      if (data.token) {
        localStorage.setItem("token", data.token);
        console.log("Token guardado ✅", data.token);
      }

      // Probar authFetch (opcional)
      const prueba = await authFetch("http://127.0.0.1:8000/api/usuarios");
      console.log("Prueba authFetch:", prueba);

      navigate("/");
    } catch (err) {
      console.error("Error en Login.jsx:", err);
      setError("Error del servidor");
    }
  };

  // 🔹 Recuperar contraseña
  const handleRecover = async (e) => {
    e.preventDefault();
    setRecoverMsg("");

    try {
      const res = await fetch("http://127.0.0.1:8000/api/users/recover-password", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email: recoverEmail }),
      });

      const data = await res.json();

      if (!res.ok) {
        setRecoverMsg(data.error || "❌ No se pudo enviar el correo.");
        return;
      }

      setRecoverMsg("✅ Se ha enviado un correo de recuperación a tu email.");
    } catch (err) {
      console.error("Error al recuperar contraseña:", err);
      setRecoverMsg("❌ Error del servidor.");
    }
  };

  return (
    <div className="page-center">
      <div className="form-container">
        <h1>Iniciar sesión</h1>

        {error && <p style={{ color: "red" }}>{error}</p>}

        {/* 🔹 FORMULARIO LOGIN */}
        {!recoverMode ? (
          <form onSubmit={handleLogin}>
            <input
              type="email"
              placeholder="Correo electrónico"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
            <input
              type="password"
              placeholder="Contraseña"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
            <button type="submit">Entrar</button>

            {/* Botón para recuperar contraseña */}
            <p
              onClick={() => {
                setRecoverMode(true);
                setRecoverMsg("");
              }}
              style={{
                marginTop: "0.8rem",
                color: "#007bff",
                cursor: "pointer",
                textAlign: "center",
              }}
            >
              ¿Olvidaste tu contraseña?
            </p>
          </form>
        ) : (
          /* 🔹 FORMULARIO RECUPERACIÓN */
          <form onSubmit={handleRecover}>
            <h3>Recuperar contraseña</h3>
            <input
              type="email"
              placeholder="Introduce tu correo"
              value={recoverEmail}
              onChange={(e) => setRecoverEmail(e.target.value)}
              required
            />
            <button type="submit">Enviar correo</button>
            {recoverMsg && (
              <p
                style={{
                  color: recoverMsg.startsWith("✅") ? "green" : "red",
                  marginTop: "0.5rem",
                }}
              >
                {recoverMsg}
              </p>
            )}
            <p
              onClick={() => setRecoverMode(false)}
              style={{
                marginTop: "1rem",
                color: "#007bff",
                cursor: "pointer",
                textAlign: "center",
              }}
            >
              ← Volver al inicio de sesión
            </p>
          </form>
        )}

        {/* Botón de registro */}
        {!recoverMode && (
          <p style={{ marginTop: "1rem", textAlign: "center" }}>
            ¿No tienes cuenta?{" "}
            <Link to="/registro" className="btn-register">
              Registrarse
            </Link>
          </p>
        )}
      </div>
    </div>
  );
}
