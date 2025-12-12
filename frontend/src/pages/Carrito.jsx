import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { authFetch } from "../pages/admin/utils/api";

export default function Carrito({ carrito, setCarrito, usuario }) {
  const navigate = useNavigate();

  const [mensaje, setMensaje] = useState(null);
  const [showModal, setShowModal] = useState(false);

  const total = carrito.reduce(
    (acc, p) => acc + Number(p.precio) * Number(p.cantidad),
    0
  );

  const eliminarProducto = (id) => {
    const productoExistente = carrito.find((p) => p.id === id);
    if (!productoExistente) return;

    if (productoExistente.cantidad > 1) {
      setCarrito(
        carrito.map((p) =>
          p.id === id ? { ...p, cantidad: p.cantidad - 1 } : p
        )
      );
    } else {
      setCarrito(carrito.filter((p) => p.id !== id));
    }
  };

  const confirmarVaciar = () => {
    setCarrito([]);
    localStorage.removeItem("carrito");

    setMensaje({ tipo: "warning", texto: "🗑️ Carrito vaciado correctamente." });
    setShowModal(false);
  };

  const hacerPedido = async () => {
    if (!usuario) {
      setMensaje({
        tipo: "danger",
        texto: "⚠️ Debes iniciar sesión para hacer un pedido.",
      });
      return;
    }

    if (carrito.length === 0) {
      setMensaje({ tipo: "warning", texto: "Tu carrito está vacío." });
      return;
    }

    try {
      const pedido = {
        usuario_id: usuario.id,
        productos: carrito.map((p) => ({
          id: p.id,
          cantidad: p.cantidad,
        })),
        metodo_pago: "contra_reembolso",
      };

      const res = await authFetch("http://127.0.0.1:8000/api/pedidos", {
        method: "POST",
        body: JSON.stringify(pedido),
      });

      if (!res.ok) throw new Error();

      const data = await res.json();

      setMensaje({ tipo: "success", texto: "Pedido realizado con éxito 🎉" });

      setCarrito([]);
      localStorage.removeItem("carrito");

      navigate(`/pedido-pago/${data.pedido_id}`);
    } catch (error) {
      setMensaje({
        tipo: "danger",
        texto: "❌ Ocurrió un error al procesar tu pedido.",
      });
    }
  };

  return (
    <div className="carrito-container">
      <h1 className="carrito-titulo">🛍️ Tu carrito</h1>
      {mensaje && (
        <div className={`alert alert-${mensaje.tipo}`} role="alert">
          {mensaje.texto}
        </div>
      )}

      {carrito.length === 0 ? (
        <p className="text-center text-muted">
          Tu carrito está vacío. Añade productos desde la tienda 🍬
        </p>
      ) : (
        <>
          <div className="table-responsive">
            <table className="table table-striped">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Precio Unitario</th>
                  <th>Cantidad</th>
                  <th>Subtotal</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                {carrito.map((p) => (
                  <tr key={p.id}>
                    <td>{p.nombre}</td>
                    <td>{Number(p.precio).toFixed(2)} €</td>
                    <td>{p.cantidad}</td>
                    <td>{(Number(p.precio) * p.cantidad).toFixed(2)} €</td>
                    <td>
                      <button
                        className="btn-eliminar"
                        onClick={() => eliminarProducto(p.id)}
                      >
                        🗑️
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          <p className="carrito-total">Total: {total.toFixed(2)} €</p>

          <div className="carrito-botones">
            <button className="btn-vaciar" onClick={() => setShowModal(true)}>
              Vaciar carrito
            </button>
            <button className="btn-pedido" onClick={hacerPedido}>
              Hacer pedido 🧾
            </button>
          </div>
        </>
      )}

      {showModal && (
        <div
          className="modal fade show d-block"
          tabIndex="-1"
          style={{ backgroundColor: "rgba(0,0,0,0.5)" }}
        >
          <div className="modal-dialog modal-dialog-centered">
            <div className="modal-content">

              <div className="modal-header">
                <h5 className="modal-title">Vaciar carrito</h5>
                <button
                  type="button"
                  className="btn-close"
                  onClick={() => setShowModal(false)}
                ></button>
              </div>

              <div className="modal-body">
                <p>¿Seguro que deseas vaciar el carrito?</p>
              </div>

              <div className="modal-footer">
                <button
                  className="btn btn-secondary"
                  onClick={() => setShowModal(false)}
                >
                  Cancelar
                </button>
                <button className="btn btn-danger" onClick={confirmarVaciar}>
                  Vaciar
                </button>
              </div>

            </div>
          </div>
        </div>
      )}
    </div>
  );
}
