// src/components/Chevaux/ChevalForm.jsx
import React, { useState, useEffect } from "react";
import api from "../../api/api";

const ChevalForm = ({ cheval, onClose, onSubmit }) => {
  const [formData, setFormData] = useState({
    nom: "",
    ddn: "",
    pays: "",
    owner_id: "",
    jockey_id: ""
  });

  useEffect(() => {
    if (cheval) {
      setFormData({
        nom: cheval.nom || "",
        ddn: cheval.ddn || "",
        pays: cheval.pays || "",
        owner_id: cheval.owner_id || "",
        jockey_id: cheval.jockey_id || ""
      });
    } else {
      setFormData({
        nom: "",
        ddn: "",
        pays: "",
        owner_id: "",
        jockey_id: ""
      });
    }
  }, [cheval]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (cheval) {
        // Modification
        await api.put(`/chevaux/${cheval.id}`, formData);
        alert("Cheval modifié !");
      } else {
        // Ajout
        await api.post("/chevaux", formData);
        alert("Cheval ajouté !");
      }
      onSubmit();
    } catch (err) {
      console.error(err);
      alert("Erreur lors de l'enregistrement");
    }
  };

  return (
    <div className="card mb-3 p-3">
      <h5>{cheval ? "Modifier Cheval" : "Ajouter Cheval"}</h5>
      <form onSubmit={handleSubmit}>
        <div className="mb-2">
          <input
            type="text"
            name="nom"
            className="form-control"
            placeholder="Nom"
            value={formData.nom}
            onChange={handleChange}
            required
          />
        </div>
        <div className="mb-2">
          <input
            type="date"
            name="ddn"
            className="form-control"
            placeholder="Date de naissance"
            value={formData.ddn}
            onChange={handleChange}
            required
          />
        </div>
        <div className="mb-2">
          <input
            type="text"
            name="pays"
            className="form-control"
            placeholder="Pays"
            value={formData.pays}
            onChange={handleChange}
            required
          />
        </div>
        <div className="mb-2">
          <input
            type="number"
            name="owner_id"
            className="form-control"
            placeholder="Owner ID"
            value={formData.owner_id}
            onChange={handleChange}
            required
          />
        </div>
        <div className="mb-2">
          <input
            type="number"
            name="jockey_id"
            className="form-control"
            placeholder="Jockey ID"
            value={formData.jockey_id}
            onChange={handleChange}
            required
          />
        </div>
        <button type="submit" className="btn btn-primary me-2">
          {cheval ? "Modifier" : "Ajouter"}
        </button>
        <button type="button" className="btn btn-secondary" onClick={onClose}>
          Annuler
        </button>
      </form>
    </div>
  );
};

export default ChevalForm;
