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
    'validated');

INSERT INTO users
VALUES (
    'shannon@gmail.com',
    'cestmoishannon',
    'Martin',
    'Shannon',
    '0671891724',
    'validator',
    'validated');

INSERT INTO users
VALUES (
    'olivia@gmail.com',
    'cestmoiolivia',
    'Bertrand',
    'Olivia',
    '0671891713',
    'annotator',
    'validated');

INSERT INTO users
VALUES (
    'joshua@gmail.com',
    'cestmoijoshua',
    'Bertrand',
    'Joshua',
    '0671891756',
    'reader',
    'validated');

end transaction;
