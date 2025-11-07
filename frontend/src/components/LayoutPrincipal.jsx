import Header from "./Header";
import Navbar from "./Navbar";
import Footer from "./Footer";

const LayoutPrincipal = ({ children, usuario, setUsuario }) => {
  return (
    <div style={{ display: "flex", flexDirection: "column", minHeight: "100vh" }}>
      <Header />
      <Navbar usuario={usuario} setUsuario={setUsuario} />
      <main className="main-content">{children}</main>
      <Footer />
    </div>
  );
};

export default LayoutPrincipal;
