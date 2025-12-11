import { useEffect, useState } from "react";
import { authFetch } from "../pages/admin/utils/api";

export default function MisPedidos() {
    const [pedidos, setPedidos] = useState([]);
    const [cargando, setCargando] = useState(true);
    const [error, setError] = useState("");

    const usuario = JSON.parse(localStorage.getItem("usuario"));

    useEffect(() => {
        if (!usuario) {
            setError("Debes iniciar sesión para ver tus pedidos.");
            setCargando(false);
            return;
        }

        const cargarPedidos = async () => {
            try {
                const response = await authFetch(
                    `http://127.0.0.1:8000/api/mis-pedidos?usuario_id=${usuario.id}`
                );

                if (!response.ok) {
                    throw new Error("Error al obtener tus pedidos.");
                }

                const data = await response.json();
                setPedidos(data);
            } catch (err) {
                setError(err.message);
            } finally {
                setCargando(false);
            }
        };

        cargarPedidos();
    }, [usuario]);

    if (cargando) return <p className="cargando">Cargando pedidos...</p>;
    if (error) return <p className="error">{error}</p>;

    return (
        <div className="mis-pedidos-container">
            <h2 className="titulo-mispedidos">Mis pedidos</h2>

            {pedidos.length === 0 ? (
                <p className="sin-pedidos">No tienes pedidos aún.</p>
            ) : (
                <div className="pedidos-grid">
                    {pedidos.map((pedido) => (
                        <div className="pedido-card" key={pedido.id}>
                            <h3>Pedido #{pedido.id}</h3>
                            <p><strong>Fecha:</strong> {pedido.fecha}</p>
                            <p><strong>Estado:</strong> {pedido.estado}</p>
                            <p><strong>Total:</strong> {pedido.total} €</p>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
