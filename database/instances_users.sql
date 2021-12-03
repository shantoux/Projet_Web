------------------------------------------------------
-- Debut de la transaction pour les users
------------------------------------------------------
Begin transaction;

INSERT INTO users
VALUES (
    'bobby@gmail.com',
    'cestmoibobby',
    'Dupont',
    'Bobby',
    '0671891726',
    'administrator',
    'validated',
    '2021-12-02');

INSERT INTO users
VALUES (
    'shannon@gmail.com',
    'cestmoishannon',
    'Martin',
    'Shannon',
    '0671891724',
    'validator',
    'validated',
    '2021-12-02');

INSERT INTO users
VALUES (
    'olivia@gmail.com',
    'cestmoiolivia',
    'Bertrand',
    'Olivia',
    '0671891713',
    'annotator',
    'validated',
    '2021-12-02');

INSERT INTO users
VALUES (
    'joshua@gmail.com',
    'cestmoijoshua',
    'Bertrand',
    'Joshua',
    '0671891756',
    'reader',
    'validated',
    '2021-12-02');

end transaction;
