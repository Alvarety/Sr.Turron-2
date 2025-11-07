const LayoutMinimal = ({ children }) => {
  return (
    <div style={{ display: "flex", justifyContent: "center", alignItems: "center", minHeight: "100vh" }}>
      {children}
    </div>
  );
};

export default LayoutMinimal;
