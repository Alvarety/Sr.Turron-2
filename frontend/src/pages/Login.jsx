import { useState } from "react";
import { useNavigate, Link } from "react-router-dom";

export default function Login({ setUsuario }) {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const navigate = useNavigate();

  const handleLogin = async (e) => {
    e.preventDefault();
    setError("");

    try {
      const res = await fetch("http://localhost:8000/api/users/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });

      const data = await res.json();

      if (!res.ok) {
        setError(data.error || "Error al iniciar sesión");
        return;
      }

      // ✅ Guardar usuario
      setUsuario(data.usuario);
      localStorage.setItem("usuario", JSON.stringify(data.usuario));

      // ✅ Guardar token (LO IMPORTANTE)
      if (data.token) {
        localStorage.setItem("token", data.token);
        console.log("Token guardado ✅", data.token);
      } else {
        console.warn("⚠️ El backend no está enviando token.");
      }

      navigate("/");

    } catch (err) {
      console.error(err);
      setError("Error del servidor");
    }
  };


  return (
    <div className="page-center">
      <div className="form-container">
        <h1>Iniciar sesión</h1>
        {error && <p style={{ color: "red" }}>{error}</p>}
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
        </form>

        {/* Botón de registrarse */}
        <p style={{ marginTop: "1rem", textAlign: "center" }}>
          ¿No tienes cuenta?{" "}
          <Link to="/registro" className="btn-register">
            Registrarse
          </Link>
        </p>
      </div>
    </div>
  );
}
