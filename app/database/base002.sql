USE bngrc;

CREATE TABLE bngrc_dispatch (
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_id INT NOT NULL,
    besoin_id INT NOT NULL,
    quantite_attribuee INT NOT NULL,
    date_dispatch DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_dispatch_don
        FOREIGN KEY (don_id)
        REFERENCES bngrc_don(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_dispatch_besoin
        FOREIGN KEY (besoin_id)
        REFERENCES bngrc_besoin(id)
        ON DELETE CASCADE
);

