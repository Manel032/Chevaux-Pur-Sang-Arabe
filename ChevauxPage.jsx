// src/pages/ChevauxPage.jsx
import React, { useState } from "react";
import ChevalList from "../components/Chevaux/ChevalList";
import ChevalForm from "../components/Chevaux/ChevalForm";
import ChevalDetail from "../components/Chevaux/ChevalDetail";

const ChevauxPage = () => {
  const [selectedCheval, setSelectedCheval] = useState(null); // pour modifier ou voir détails
  const [showForm, setShowForm] = useState(false);
  const [showDetail, setShowDetail] = useState(false);

  // Afficher le formulaire pour ajouter un nouveau cheval
  const handleAdd = () => {
    setSelectedCheval(null);
    setShowForm(true);
    setShowDetail(false);
  };

  // Afficher le formulaire pour modifier un cheval
  const handleEdit = (cheval) => {
    setSelectedCheval(cheval);
    setShowForm(true);
    setShowDetail(false);
  };

  // Afficher les détails d’un cheval
  const handleView = (cheval) => {
    setSelectedCheval(cheval);
    setShowDetail(true);
    setShowForm(false);
  };

  // Callback après ajout/modification
  const handleFormSubmit = () => {
    setShowForm(false);
    setSelectedCheval(null);
  };

  return (
    <div className="container my-4">
      <h1 className="text-center mb-4">Gestion des Chevaux</h1>

      <button className="btn btn-success mb-3" onClick={handleAdd}>
        Ajouter un Cheval
      </button>

      {/* Formulaire */}
      {showForm && (
        <ChevalForm
          cheval={selectedCheval}
          onClose={() => setShowForm(false)}
          onSubmit={handleFormSubmit}
        />
      )}

      {/* Détails */}
      {showDetail && selectedCheval && (
        <ChevalDetail
          chevalId={selectedCheval.id}
          onClose={() => setShowDetail(false)}
        />
      )}

      {/* Liste des chevaux */}
      <ChevalList
        onEdit={handleEdit}
        onView={handleView}
      />
    </div>
  );
};

export default ChevauxPage;
