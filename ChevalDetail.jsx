// src/components/Chevaux/ChevalDetail.jsx
import React, { useEffect, useState } from "react";
import api from "../../api/api";

const ChevalDetail = ({ chevalId, onClose }) => {
  const [cheval, setCheval] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!chevalId) return;
    const fetchCheval = async () => {
      setLoading(true);
      try {
        const res = await api.get(`/chevaux/${chevalId}`);
        setCheval(res.data.data);
      } catch (err) {
        console.error("Erreur lors du chargement du cheval :", err);
        alert("Impossible de charger le cheval");
      }
      setLoading(false);
    };

    fetchCheval();
  }, [chevalId]);

  if (!chevalId) return null;

  return (
    <div className="card p-3 mb-3">
      {loading ? (
        <p>Chargement...</p>
      ) : (
        <>
          <h5>Détails du Cheval</h5>
          <p><strong>Nom :</strong> {cheval.nom}</p>
          <p><strong>Date de naissance :</strong> {cheval.ddn}</p>
          <p><strong>Pays :</strong> {cheval.pays}</p>
          <p><strong>Owner ID :</strong> {cheval.owner_id}</p>
          <p><strong>Jockey ID :</strong> {cheval.jockey_id}</p>
          <p><strong>Âge :</strong> {cheval.age || "N/A"} ans</p>
          <button className="btn btn-secondary" onClick={onClose}>Fermer</button>
        </>
      )}
    </div>
  );
};

export default ChevalDetail;
