import { useState } from "react";
import { useNavigate, useLocation } from "react-router-dom";

export default function ResetPassword() {
  const [password, setPassword] = useState("");
  const [repeat, setRepeat] = useState("");
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const navigate = useNavigate();
  const location = useLocation();

  // Extraer el token de la URL
  const token = new URLSearchParams(location.search).get("token");

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setSuccess("");

    if (!password || !repeat) {
      setError("Rellena ambos campos.");
      return;
    }
    if (password !== repeat) {
      setError("Las contraseñas no coinciden.");
      return;
    }

    try {
      const res = await fetch("http://127.0.0.1:8000/api/users/reset-password", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ token, new_password: password }),
      });

      const data = await res.json();

      if (!res.ok) {
        setError(data.error || "Error al restablecer contraseña.");
        return;
      }

      setSuccess("✅ Contraseña restablecida correctamente.");
      setTimeout(() => navigate("/login"), 2000);

    } catch (err) {
      console.error(err);
      setError("Error del servidor.");
    }
  };

  return (
    <div className="page-center">
      <div className="form-container">
        <h1>Restablecer Contraseña</h1>
        {!token ? (
          <p style={{ color: "red" }}>Token no válido o expirado.</p>
        ) : (
          <form onSubmit={handleSubmit}>
            <input
              type="password"
              placeholder="Nueva contraseña"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
            <input
              type="password"
              placeholder="Repetir contraseña"
              value={repeat}
              onChange={(e) => setRepeat(e.target.value)}
              required
            />
            <button type="submit">Cambiar contraseña</button>
          </form>
        )}
        {error && <p style={{ color: "red", marginTop: "1rem" }}>{error}</p>}
        {success && <p style={{ color: "green", marginTop: "1rem" }}>{success}</p>}
      </div>
    </div>
  );
}
