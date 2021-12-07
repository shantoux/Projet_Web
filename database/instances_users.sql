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
    'Administrator',
    'validated');

INSERT INTO users
VALUES (
    'shannon@gmail.com',
    'cestmoishannon',
    'Martin',
    'Shannon',
    '0671891724',
    'Validator',
    'validated');

INSERT INTO users
VALUES (
    'olivia@gmail.com',
    'cestmoiolivia',
    'Bertrand',
    'Olivia',
    '0671891713',
    'Annotator',
    'validated');

INSERT INTO users
VALUES (
    'melina@gmail.com',
    'cestmoimelina',
    'Hanaux',
    'Melina',
    '0671491712',
    'Annotator',
    'validated');

INSERT INTO users
VALUES (
    'joshua@gmail.com',
    'cestmoijoshua',
    'Bertrand',
    'Joshua',
    '0671891756',
    'Reader',
    'validated');

end transaction;
