// src/components/Chevaux/ChevalList.jsx
import React, { useEffect, useState } from "react";
import api from "../../api/api";
import ChevalForm from "./ChevalForm";

const ChevalList = () => {
  const [chevaux, setChevaux] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedCheval, setSelectedCheval] = useState(null);
  const [showForm, setShowForm] = useState(false);

  // Charger la liste des chevaux
  const loadChevaux = async () => {
    setLoading(true);
    try {
      const res = await api.get("/chevaux");
      setChevaux(res.data.data || []);
    } catch (err) {
      console.error("Erreur lors du chargement des chevaux:", err);
      alert("Erreur lors du chargement des chevaux");
    }
    setLoading(false);
  };

  useEffect(() => {
    loadChevaux();
  }, []);

  // Supprimer un cheval
  const deleteCheval = async (id) => {
    if (!window.confirm("Voulez-vous vraiment supprimer ce cheval ?")) return;
    try {
      await api.delete(`/chevaux/${id}`);
      setChevaux(chevaux.filter((c) => c.id !== id));
      alert("Cheval supprimé !");
    } catch (err) {
      console.error(err);
      alert("Erreur lors de la suppression");
    }
  };

  // Modifier un cheval
  const editCheval = (cheval) => {
    setSelectedCheval(cheval);
    setShowForm(true);
  };

  // Ajouter un nouveau cheval
  const addCheval = () => {
    setSelectedCheval(null);
    setShowForm(true);
  };

  // Callback après ajout/modification
  const onFormSubmit = () => {
    setShowForm(false);
    loadChevaux();
  };

  return (
    <div>
      <h2>Chevaux</h2>
      <button className="btn btn-success mb-3" onClick={addCheval}>
        Ajouter un Cheval
      </button>

      {showForm && (
        <ChevalForm
          cheval={selectedCheval}
          onClose={() => setShowForm(false)}
          onSubmit={onFormSubmit}
        />
      )}

      {loading ? (
        <p>Chargement...</p>
      ) : (
        <ul className="list-group">
          {chevaux.map((c) => (
            <li
              key={c.id}
              className="list-group-item d-flex justify-content-between align-items-center"
            >
              {c.nom} ({c.age || "N/A"} ans) - Owner ID: {c.owner_id} - Jockey ID: {c.jockey_id}
              <span>
                <button
                  className="btn btn-sm btn-warning me-1"
                  onClick={() => editCheval(c)}
                >
                  Modifier
                </button>
                <button
                  className="btn btn-sm btn-danger"
                  onClick={() => deleteCheval(c.id)}
                >
                  Supprimer
                </button>
              </span>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
};

export default ChevalList;
