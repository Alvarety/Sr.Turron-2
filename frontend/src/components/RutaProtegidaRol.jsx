import { Navigate } from "react-router-dom";

export default function RutaProtegidaRol({ usuario, rolesPermitidos, children }) {
  if (!usuario) return <Navigate to="/login" replace />;

  if (!rolesPermitidos.includes(usuario.rol)) return <Navigate to="/" replace />;

  return children;
}
