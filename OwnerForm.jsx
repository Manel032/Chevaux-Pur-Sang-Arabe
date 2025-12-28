// src/components/Owners/OwnerForm.jsx
import React, { useState, useEffect } from "react";
import api from "../../api/api";

const OwnerForm = ({ owner, onClose, onSubmit }) => {
  const [formData, setFormData] = useState({
    nom: "",
    pays: "",
    email: "",
    tel: ""
  });

  // Remplir le formulaire si un owner est sélectionné pour modification
  useEffect(() => {
    if (owner) {
      setFormData({
        nom: owner.nom || "",
        pays: owner.pays || "",
        email: owner.email || "",
        tel: owner.tel || ""
      });
    } else {
      setFormData({
        nom: "",
        pays: "",
        email: "",
        tel: ""
      });
    }
  }, [owner]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (owner) {
        // Modification
        await api.put(`/owners/${owner.id}`, formData);
        alert("Owner modifié !");
      } else {
        // Ajout
        await api.post("/owners", formData);
        alert("Owner ajouté !");
      }
      onSubmit(); // Refresh liste
    } catch (err) {
      console.error(err);
      alert("Erreur lors de l'enregistrement");
    }
  };

  return (
    <div className="card mb-3 p-3">
      <h5>{owner ? "Modifier Owner" : "Ajouter Owner"}</h5>
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
            type="email"
            name="email"
            className="form-control"
            placeholder="Email"
            value={formData.email}
            onChange={handleChange}
          />
        </div>
        <div className="mb-2">
          <input
            type="text"
            name="tel"
            className="form-control"
            placeholder="Téléphone"
            value={formData.tel}
            onChange={handleChange}
          />
        </div>
        <button type="submit" className="btn btn-primary me-2">
          {owner ? "Modifier" : "Ajouter"}
        </button>
        <button type="button" className="btn btn-secondary" onClick={onClose}>
          Annuler
        </button>
      </form>
    </div>
  );
};

export default OwnerForm;
