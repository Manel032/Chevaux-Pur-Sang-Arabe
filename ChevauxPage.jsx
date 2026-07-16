// ChevauxPage.jsx - React page container for Horses section
const { useState, useEffect } = React;

function ChevauxPage() {
    const [viewMode, setViewMode] = useState('list'); // 'list' or 'form'
    const [selectedHorseId, setSelectedHorseId] = useState(null);
    const [editHorseId, setEditHorseId] = useState(null);
    const [stats, setStats] = useState({
        total: 0,
        arabe: 0,
        barbe: 0,
        arabeBarbe: 0
    });
    const [refreshTrigger, setRefreshTrigger] = useState(0);

    useEffect(() => {
        calculateStats();
    }, [refreshTrigger]);

    const calculateStats = async () => {
        try {
            const res = await axios.get('api.php?action=get_chevaux');
            if (res.data.success) {
                const list = res.data.data;
                const arabes = list.filter(h => h.race === 'Pur-Sang Arabe').length;
                const barbes = list.filter(h => h.race === 'Barbe').length;
                const arabeBarbes = list.filter(h => h.race === 'Arabe-Barbe').length;
                
                setStats({
                    total: list.length,
                    arabe: arabes,
                    barbe: barbes,
                    arabeBarbe: arabeBarbes
                });
            }
        } catch (err) {
            console.error("Erreur statistiques", err);
        }
    };

    const handleSave = () => {
        setViewMode('list');
        setEditHorseId(null);
        setRefreshTrigger(prev => prev + 1);
    };

    const handleEdit = (id) => {
        setEditHorseId(id);
        setViewMode('form');
    };

    const handleDelete = async (id) => {
        if (window.confirm("Êtes-vous sûr de vouloir retirer ce cheval du registre national ?")) {
            try {
                const res = await axios.post(`api.php?action=delete_cheval&id=${id}`);
                if (res.data.success) {
                    setRefreshTrigger(prev => prev + 1);
                } else {
                    alert(res.data.message || "Erreur de suppression");
                }
            } catch (err) {
                console.error("Erreur suppression", err);
                alert("Erreur de communication avec le serveur.");
            }
        }
    };

    const handleCreateNew = () => {
        setEditHorseId(null);
        setViewMode('form');
    };

    return (
        <div className="tab-content">
            <div className="section-header">
                <h2>Registre des Chevaux de Tunisie</h2>
                {viewMode === 'list' ? (
                    <button className="btn btn-primary" onClick={handleCreateNew}>
                        ➕ Enregistrer un Cheval
                    </button>
                ) : (
                    <button className="btn btn-secondary" onClick={() => setViewMode('list')}>
                        ⬅️ Retour au Registre
                    </button>
                )}
            </div>

            {/* Herd Statistics (only on list view) */}
            {viewMode === 'list' && (
                <div className="dashboard-stats">
                    <div className="glass-panel stat-card">
                        <div className="stat-icon">🐎</div>
                        <div className="stat-info">
                            <span className="stat-number">{stats.total}</span>
                            <span className="stat-label">Total Enregistrés</span>
                        </div>
                    </div>
                    <div className="glass-panel stat-card" style={{borderLeft: '4px solid #d4af37'}}>
                        <div className="stat-info">
                            <span className="stat-number">{stats.arabe}</span>
                            <span className="stat-label">Pur-Sang Arabes</span>
                        </div>
                    </div>
                    <div className="glass-panel stat-card" style={{borderLeft: '4px solid #2ecc71'}}>
                        <div className="stat-info">
                            <span className="stat-number">{stats.barbe}</span>
                            <span className="stat-label">Barbes Tunisiens</span>
                        </div>
                    </div>
                    <div className="glass-panel stat-card" style={{borderLeft: '4px solid #3498db'}}>
                        <div className="stat-info">
                            <span className="stat-number">{stats.arabeBarbe}</span>
                            <span className="stat-label">Arabe-Barbes</span>
                        </div>
                    </div>
                </div>
            )}

            {/* List or Form Panel */}
            {viewMode === 'list' ? (
                <window.ChevalList 
                    onViewDetail={setSelectedHorseId}
                    onEdit={handleEdit}
                    onDelete={handleDelete}
                    refreshTrigger={refreshTrigger}
                />
            ) : (
                <window.ChevalForm 
                    horseId={editHorseId}
                    onSave={handleSave}
                    onCancel={() => setViewMode('list')}
                />
            )}

            {/* Pedigree Detail Modal Popup */}
            {selectedHorseId && (
                <window.ChevalDetail 
                    horseId={selectedHorseId} 
                    onClose={() => setSelectedHorseId(null)} 
                />
            )}
        </div>
    );
}

window.ChevauxPage = ChevauxPage;
