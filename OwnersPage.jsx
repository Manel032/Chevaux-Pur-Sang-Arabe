// OwnersPage.jsx - React page container for Owners section
const { useState, useEffect } = React;

function OwnersPage() {
    const [viewMode, setViewMode] = useState('list'); // 'list' or 'form'
    const [editOwnerId, setEditOwnerId] = useState(null);
    const [refreshTrigger, setRefreshTrigger] = useState(0);
    const [totalOwners, setTotalOwners] = useState(0);

    useEffect(() => {
        calculateStats();
    }, [refreshTrigger]);

    const calculateStats = async () => {
        try {
            const res = await axios.get('api.php?action=get_owners');
            if (res.data.success) {
                setTotalOwners(res.data.data.length);
            }
        } catch (err) {
            console.error("Erreur stats propriétaires", err);
        }
    };

    const handleSave = () => {
        setViewMode('list');
        setEditOwnerId(null);
        setRefreshTrigger(prev => prev + 1);
    };

    const handleEdit = (id) => {
        setEditOwnerId(id);
        setViewMode('form');
    };

    const handleDelete = async (id) => {
        if (window.confirm("Êtes-vous sûr de vouloir supprimer ce propriétaire ? Les chevaux associés n'auront plus de propriétaire rattaché.")) {
            try {
                const res = await axios.post(`api.php?action=delete_owner&id=${id}`);
                if (res.data.success) {
                    setRefreshTrigger(prev => prev + 1);
                } else {
                    alert(res.data.message || "Erreur lors de la suppression.");
                }
            } catch (err) {
                console.error("Erreur suppression", err);
                alert("Erreur lors de la communication avec le serveur.");
            }
        }
    };

    const handleCreateNew = () => {
        setEditOwnerId(null);
        setViewMode('form');
    };

    return (
        <div className="tab-content">
            <div className="section-header">
                <h2>Registre des Éleveurs & Haras Nationaux</h2>
                {viewMode === 'list' ? (
                    <button className="btn btn-primary" onClick={handleCreateNew}>
                        ➕ Ajouter un Propriétaire / Haras
                    </button>
                ) : (
                    <button className="btn btn-secondary" onClick={() => setViewMode('list')}>
                        ⬅️ Retour au Registre
                    </button>
                )}
            </div>

            {viewMode === 'list' && (
                <div className="dashboard-stats" style={{gridTemplateColumns:'1fr'}}>
                    <div className="glass-panel stat-card" style={{maxWidth:'350px'}}>
                        <div className="stat-icon">🏢</div>
                        <div className="stat-info">
                            <span className="stat-number">{totalOwners}</span>
                            <span className="stat-label">Haras & Propriétaires Enregistrés</span>
                        </div>
                    </div>
                </div>
            )}

            {viewMode === 'list' ? (
                <window.OwnerList 
                    onEdit={handleEdit}
                    onDelete={handleDelete}
                    refreshTrigger={refreshTrigger}
                />
            ) : (
                <window.OwnerForm 
                    ownerId={editOwnerId}
                    onSave={handleSave}
                    onCancel={() => setViewMode('list')}
                />
            )}
        </div>
    );
}

window.OwnersPage = OwnersPage;
