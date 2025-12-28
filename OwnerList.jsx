// src/components/Owners/OwnerList.jsx
import React, { useEffect, useState } from "react";
import api from "../../api/api";
import OwnerForm from "./OwnerForm";

const OwnerList = () => {
  const [owners, setOwners] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedOwner, setSelectedOwner] = useState(null);
  const [showForm, setShowForm] = useState(false);

  // Charger la liste des owners
  const loadOwners = async () => {
    setLoading(true);
    try {
      const res = await api.get("/owners");
      setOwners(res.data.data || []);
    } catch (err) {
      console.error("Erreur lors du chargement des owners:", err);
      alert("Erreur lors du chargement des owners");
    }
    setLoading(false);
  };

  useEffect(() => {
    loadOwners();
  }, []);

  // Supprimer un owner
  const deleteOwner = async (id) => {
    if (!window.confirm("Voulez-vous vraiment supprimer ce owner ?")) return;
    try {
      await api.delete(`/owners/${id}`);
      setOwners(owners.filter((o) => o.id !== id));
      alert("Owner supprimé !");
    } catch (err) {
      console.error(err);
      alert("Erreur lors de la suppression");
    }
  };

  // Modifier un owner
  const editOwner = (owner) => {
    setSelectedOwner(owner);
    setShowForm(true);
  };

  // Ajouter un nouveau owner
  const addOwner = () => {
    setSelectedOwner(null);
    setShowForm(true);
  };

  // Callback après ajout/modification
  const onFormSubmit = () => {
    setShowForm(false);
    loadOwners();
  };

  return (
    <div>
      <h2>Owners</h2>
      <button className="btn btn-success mb-3" onClick={addOwner}>
        Ajouter un Owner
      </button>

      {showForm && (
        <OwnerForm
          owner={selectedOwner}
          onClose={() => setShowForm(false)}
          onSubmit={onFormSubmit}
        />
      )}

      {loading ? (
        <p>Chargement...</p>
      ) : (
        <ul className="list-group">
          {owners.map((o) => (
            <li
              key={o.id}
              className="list-group-item d-flex justify-content-between align-items-center"
            >
              {o.nom} - {o.pays} - {o.email || ""} - {o.tel || ""}
              <span>
                <button
                  className="btn btn-sm btn-warning me-1"
                  onClick={() => editOwner(o)}
                >
                  Modifier
                </button>
                <button
                  className="btn btn-sm btn-danger"
                  onClick={() => deleteOwner(o.id)}
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

export default OwnerList;
