import { useState } from "react";
import { useNavigate } from "react-router-dom";

export default function Registro() {
  const [nombre, setNombre] = useState("");
  const [apellido1, setApellido1] = useState("");
  const [apellido2, setApellido2] = useState("");
  const [nickname, setNickname] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [telefono, setTelefono] = useState("");
  const [direccion, setDireccion] = useState("");
  const [error, setError] = useState("");
  const navigate = useNavigate();

  const handleRegistro = async (e) => {
    e.preventDefault();
    setError("");

    try {
      const res = await fetch("http://localhost:8000/api/users/register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          nombre,
          apellido1,
          apellido2,
          nickname,
          email,
          password,
          telefono,
          direccion,
        }),
      });

      const data = await res.json();

      if (!res.ok) {
        setError(data.error || "Error al registrar usuario");
        return;
      }

      // Registro correcto → redirigir a login
      navigate("/login");
    } catch (err) {
      console.error(err);
      setError("Error del servidor");
    }
  };

  return (
    <div className="page-center">
      <div className="form-container">
        <h1>Crear cuenta</h1>
        {error && <p style={{ color: "red" }}>{error}</p>}

        <form onSubmit={handleRegistro}>
          <input
            type="text"
            placeholder="Nombre"
            value={nombre}
            onChange={(e) => setNombre(e.target.value)}
            required
          />
          <input
            type="text"
            placeholder="Primer apellido"
            value={apellido1}
            onChange={(e) => setApellido1(e.target.value)}
            required
          />
          <input
            type="text"
            placeholder="Segundo apellido (opcional)"
            value={apellido2}
            onChange={(e) => setApellido2(e.target.value)}
          />
          <input
            type="text"
            placeholder="Nombre de usuario / Nickname"
            value={nickname}
            onChange={(e) => setNickname(e.target.value)}
            required
          />
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
          <input
            type="text"
            placeholder="Teléfono"
            value={telefono}
            onChange={(e) => setTelefono(e.target.value)}
          />
          <input
            type="text"
            placeholder="Dirección"
            value={direccion}
            onChange={(e) => setDireccion(e.target.value)}
          />
          <button type="submit">Registrarse</button>
        </form>
      </div>
    </div>
  );
}
