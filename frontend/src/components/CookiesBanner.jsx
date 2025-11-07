import { useState, useEffect } from "react";

export default function CookiesBanner() {
  const [show, setShow] = useState(false);

  useEffect(() => {
    const accepted = localStorage.getItem("cookies_accepted");
    if (!accepted) setShow(true);
  }, []);

  const acceptCookies = () => {
    localStorage.setItem("cookies_accepted", "true");
    setShow(false);
  };

  if (!show) return null;

  return (
    <div style={{
      position: "fixed",
      bottom: 0,
      width: "100%",
      background: "#222",
      color: "white",
      padding: "1rem",
      textAlign: "center"
    }}>
      <p>Usamos cookies para mejorar tu experiencia en el sitio.</p>
      <button onClick={acceptCookies} style={{ marginTop: "0.5rem" }}>
        Aceptar
      </button>
    </div>
  );
}
