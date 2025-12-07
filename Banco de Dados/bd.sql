drop database if exists VolleyConnect;

create database if not exists VolleyConnect;
use VolleyConnect;

create table usuario
(
	id int not null auto_increment,
	cpf int not null,
    nome varchar(40),
    email varchar(20),
    senha varchar(20),
    perfil varchar(20), -- administrador, torcedor, atleta
    foto varchar (100),
    primary key (id)
);

create table time
(
	id int not null auto_increment,
	nome varchar (20),
    logo varchar (100),
	genero varchar(10),
    primary key (id)
);

create table campeonato
(
	id int not null auto_increment,
    nome varchar(40),
    genero varchar(10),
    primary key (id)
);

create table jogo
(
	id int not null auto_increment,
    local varchar(40),
    data datetime,
    genero varchar(10),
    placar_time1 int,
    placar_time2 int,
    placar_set1_time1 int,
	placar_set2_time1 int,
    placar_set3_time1 int,
    placar_set1_time2 int,
	placar_set2_time2 int,
    placar_set3_time2 int,
    id_campeonato int,
    id_time1 int,
    id_time2 int,
    primary key (id),
    constraint FK_Jogo_Time1 foreign key (id_time1) references time(id),
    constraint FK_Jogo_Time2 foreign key (id_time2) references time(id),
	constraint FK_Jogo_Campeonato foreign key (id_campeonato) references campeonato(id)
);

create table atleta
(
	id int not null auto_increment,
    -- nome varchar(100),
    -- cpf varchar(20),
    -- email varchar(40),
    -- senha varchar(100),
    id_time int,
    id_usuario int,
    posicao varchar(40),
    genero varchar(10),
    idade varchar(100),
    -- disponibilidade varchar(50),
    primary key (id),
    constraint FK_Atleta_Time foreign key (id_time) references time(id),
    constraint FK_Atleta_Usuario foreign key (id_usuario) references usuario(id)
);


create table torcedor_atleta_salvo
(
	id int not null auto_increment,    
    id_atleta int,
    id_torcedor int,
    data datetime,
    primary key (id),
    constraint FK_torcedor_atleta_salvo_atleta foreign key (id_atleta) references atleta(id),
    constraint FK_torcedor_atleta_salvo_torcedor foreign key (id_torcedor) references usuario(id)
);

create table atleta_encontro 
(
	id int not null auto_increment,
    id_atleta int not null,
    id_jogo int not null,
    horario_inicial datetime,
    duracao int,
    vagas int,    
    primary key (id),
    foreign key (id_atleta) references atleta (id),
    foreign key (id_jogo) references jogo (id)
);

create table atleta_encontro_torcedor
(
	id int not null auto_increment,
    id_atleta_encontro int not null,
    id_torcedor int not null ,
    primary key (id),
    foreign key (id_atleta_encontro) references atleta_encontro (id),
    foreign key (id_torcedor) references usuario (id)
);

ALTER TABLE atleta
ADD nome VARCHAR(100),
ADD foto VARCHAR(200);

ALTER TABLE atleta_encontro_torcedor
ADD COLUMN status ENUM('pendente', 'confirmado', 'recusado') DEFAULT 'pendente';


CREATE TABLE notificacao (
    id INT NOT NULL AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    mensagem VARCHAR(255),
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    visualizada TINYINT DEFAULT 0,
    PRIMARY KEY (id),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id)
);

CREATE TABLE ajuda (
  id INT NOT NULL AUTO_INCREMENT,
  id_usuario INT NOT NULL,
  pergunta TEXT NOT NULL,
  resposta TEXT DEFAULT NULL,
  data_pergunta DATETIME DEFAULT CURRENT_TIMESTAMP,
  data_resposta DATETIME DEFAULT NULL,
  status ENUM('pendente','respondida') NOT NULL DEFAULT 'pendente',
  PRIMARY KEY (id),
  FOREIGN KEY (id_usuario) REFERENCES usuario(id)
);


insert into time (id, nome, genero)
	values 
         (1, 'Sada', 'Feminino'),
         (2, 'Sesi Bauru', 'Feminino'),
         (3, 'Minas', 'Feminino'),
         (4, 'Flamengo', 'Feminino');
         
insert into time (id, nome, genero)
	values 
	     (5, 'Praia Clube', 'Masculino'),
	     (6, 'Cruzeiro', 'Masculino');
    
insert into atleta (nome, genero, posicao, idade, id_time, id_usuario)
	values 
	('Lucas Saatkamp', 'Masculino', 'Central', 39, 1, 1),
	('Otávio Pinto', 'Masculino', 'Ponteiro', 34, 6, 2),
	('Darlan Souza', 'Masculino', 'Oposto', 23, 2, 3),
	('Javad Karimi', 'Masculino', 'Levantador', 27, 3, 4),
    ('Rafael Forster', 'Masculino', 'Ponteiro', 19, 4, 5),
	('Isac Santos', 'Masculino', 'Oposto', 26, 5, 6);

    
insert into campeonato 
values  (1, 'Regional', 'Feminino'),
		(2, 'Paulista', 'Feminino'),
        (3, 'Nacional', 'Feminino');
        

 INSERT INTO usuario (nome, cpf, email, senha, perfil) 
 VALUES ('Lucas Saatkamp', '12312312332', 'lucas@gmail.com', '1212', 'atleta'),
        ('Darlan Souza', '12312332112', 'darlan@gmail.com', '1212', 'atleta'),
        ('Javad Karimi', '32132132112', 'javad@gmail.com', '1212', 'atleta'),
        ('Isac Santos', '65465445643', 'isac@gmail.com', '1212', 'atleta'),
        ('Otávio Pinto', '87667865456', 'otavio@gmail.com', '1212', 'atleta'),
        ('Rafael Forster', '86576556754', 'rafa@gmail.com', '1212', 'atleta'); 


select * from usuario;
select * from jogo;