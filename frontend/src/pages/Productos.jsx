import { useEffect, useState } from "react";
import axios from "axios";
import { Link } from "react-router-dom";

export default function Productos() {
  const [productos, setProductos] = useState([]);

  useEffect(() => {
    axios
      .get("http://127.0.0.1:8000/api/productos")
      .then((res) => setProductos(res.data))
      .catch((err) => console.error("Error cargando productos:", err));
  }, []);

  const agregarAlCarrito = (producto) => {
    // Evitar duplicados → si ya existe, solo aumentar cantidad
    const existente = carrito.find((p) => p.id === producto.id);
    if (existente) {
      setCarrito(
        carrito.map((p) =>
          p.id === producto.id ? { ...p, cantidad: p.cantidad + 1 } : p
        )
      );
    } else {
      setCarrito([...carrito, { ...producto, cantidad: 1 }]);
    }
  };

  return (
    <div className="productos-container">
      <h1 className="productos-titulo">Catálogo de Productos</h1>

      <div className="productos-grid">
        {productos.map((p) => (
          <div key={p.id} className="producto-card card-turron">
            <Link to={`/productos/${p.id}`}>
              <img
                src={p.imagenUrl}
                alt={p.nombre}
                className="producto-imagen"
              />
            </Link>
            <div className="producto-info">
              <h3>{p.nombre}</h3>
              <p className="producto-descripcion">{p.descripcion}</p>
              <p className="producto-categoria">
                <strong>Categoría:</strong>{" "}
                  {p.categoria ? p.categoria : "Sin categoría"}
              </p>
              <p className="producto-precio">{p.precio} €</p>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}