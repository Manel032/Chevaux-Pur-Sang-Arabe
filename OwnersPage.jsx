// src/pages/OwnersPage.jsx
import React, { useState } from "react";
import OwnerList from "../components/Owners/OwnerList";
import OwnerForm from "../components/Owners/OwnerForm";

const OwnersPage = () => {
  const [selectedOwner, setSelectedOwner] = useState(null);
  const [showForm, setShowForm] = useState(false);

  // Ouvrir le formulaire pour ajouter un nouveau owner
  const addOwner = () => {
    setSelectedOwner(null);
    setShowForm(true);
  };

  // Ouvrir le formulaire pour modifier un owner existant
  const editOwner = (owner) => {
    setSelectedOwner(owner);
    setShowForm(true);
  };

  // Callback après ajout/modification pour fermer le formulaire
  const onFormSubmit = () => {
    setShowForm(false);
  };

  return (
    <div className="container my-4">
      <h1 className="text-center mb-4">Gestion des Owners</h1>

      <div className="mb-3">
        <button className="btn btn-success" onClick={addOwner}>
          Ajouter un Owner
        </button>
      </div>

      {/* Formulaire */}
      {showForm && (
        <OwnerForm
          owner={selectedOwner}
          onClose={() => setShowForm(false)}
          onSubmit={onFormSubmit}
        />
      )}

      {/* Liste des owners */}
      <OwnerList onEdit={editOwner} />
    </div>
  );
};

export default OwnersPage;
