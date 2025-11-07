import { useState, useEffect } from 'react'
import './App.css'
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import RouterPrincipal from "../routers/RouterPrincipal";
import { BrowserRouter as Router } from "react-router-dom";
import axios from "axios";
axios.defaults.withCredentials = true;
import CookiesBanner from "../src/components/CookiesBanner"; // ⬅️ IMPORTAR (ajusta ruta si es necesario)

function App() {
  const [usuario, setUsuario] = useState(() => {
    try {
      const saved = localStorage.getItem("usuario");
      return saved ? JSON.parse(saved) : null;
    } catch {
      return null;
    }
  });

  useEffect(() => {
  const userData = localStorage.getItem("usuario");
    if (userData) {
      console.log("Usuario cargado:", JSON.parse(userData)); // 👀 VER CAMPOS EXACTOS
      setUsuario(JSON.parse(userData));
    }
  }, []);

  const [carrito, setCarrito] = useState(() => {
    try {
      const saved = localStorage.getItem("carrito");
      return saved ? JSON.parse(saved) : [];
    } catch {
      return [];
    }
  });

  // Cada vez que carrito cambie, lo guardamos
  useEffect(() => {
    try {
      localStorage.setItem("carrito", JSON.stringify(carrito));
    } catch (err) {
      console.error("No se pudo guardar el carrito en localStorage", err);
    }
  }, [carrito]);

  // También guardar usuario cuando cambie
  useEffect(() => {
    try {
      if (usuario) localStorage.setItem("usuario", JSON.stringify(usuario));
      else localStorage.removeItem("usuario");
    } catch (err) {
      console.error("No se pudo guardar el usuario", err);
    }
  }, [usuario]);

  return (
    <>
      <RouterPrincipal 
        usuario={usuario} 
        setUsuario={setUsuario} 
        carrito={carrito} 
        setCarrito={setCarrito}
      />

      <CookiesBanner />
    </>
  );
}

export default App
