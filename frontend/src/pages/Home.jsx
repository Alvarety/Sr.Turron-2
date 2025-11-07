import HomeSection from "./HomeSection";
import HomeAside from "./HomeAside";

export default function Home({ usuario }) {
  const mensaje = usuario
    ? usuario.rol === "admin"
      ? "Panel de administración"
      : usuario.rol === "empleado"
      ? "Área de empleado"
      : "Bienvenido a Señor Turrón"
    : "Bienvenido a Señor Turrón";

  return (
    <div className="container my-5">
      <h1 className="text-center mb-5 text-brown fw-bold">{mensaje}</h1>

      <div className="row">
        {/* SECTION: productos destacados */}
        <section className="inicio align-items-stretch col-md-8 mb-4">
          <HomeSection usuario={usuario} />
        </section>

        {/* ASIDE: información adicional */}
        <aside className="derecho col-md-4">
          <HomeAside usuario={usuario} />
        </aside>
      </div>
    </div>
  );
}
