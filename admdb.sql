-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 02-Maio-2018 às 17:26
-- Versão do servidor: 10.1.31-MariaDB
-- PHP Version: 7.2.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admdb`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `banner`
--

CREATE TABLE `banner` (
  `id_banner` int(11) NOT NULL,
  `title` varchar(80) NOT NULL DEFAULT '0',
  `link` varchar(80) DEFAULT '0',
  `file` varchar(80) NOT NULL DEFAULT '0',
  `cor` varchar(7) NOT NULL DEFAULT '#ffffff',
  `ordem` int(2) DEFAULT '0',
  `ativo` enum('s','n') NOT NULL DEFAULT 's'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `banner`
--

INSERT INTO `banner` (`id_banner`, `title`, `link`, `file`, `cor`, `ordem`, `ativo`) VALUES
(1, 'Front-End', '#', 'S2m1Lkl.jpg', '#0665b3', 1, 's');

-- --------------------------------------------------------

--
-- Estrutura da tabela `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `label` varchar(255) COLLATE utf8_bin NOT NULL,
  `val` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `key` varchar(255) COLLATE utf8_bin NOT NULL,
  `field` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT 'text',
  `category` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Extraindo dados da tabela `config`
--

INSERT INTO `config` (`id`, `label`, `val`, `key`, `field`, `category`) VALUES
(1, 'Nome do site', 'Teste', 'site_name', 'text', 1),
(2, 'Description', 'Descrição do SEO', 'description', 'text', 1),
(3, 'Keywords', 'tags, para, seo, separadas, por, vírgula', 'keywords', 'text', 1),
(4, 'Cor Principal do site', '#f96868', 'theme_bar_color', 'text', 1),
(5, 'E-mail de recebimento', 'programador@21brz.com.br', 'email-recebimento', 'text', 2),
(6, 'Servidor SMTP', 'srv218.prodns.com.br ', 'server-smtp', 'text', 2),
(7, 'Porta SMTP', '465', 'port-smtp', 'text', 2),
(8, 'Facebook', NULL, 'social-fb', 'text', 3),
(9, 'YouTube', NULL, 'social-yt', 'text', 3),
(10, 'Telefone', NULL, 'telefone', 'text', 4),
(11, 'ID Google Analytics', NULL, 'g-analytics', 'text', 1),
(12, 'E-mail para disparo', 'programador@21brz.com.br', 'user-smtp', 'text', 2),
(13, 'Thumbnail do Facebook', '', 'fb_thumb', 'text', 1),
(14, 'Senha para disparo', 'padrao12', 'pswd-smtp', 'text', 2),
(15, 'Instagram', NULL, 'social-it', 'text', 3),
(16, 'Twitter', '', 'social-tw', 'text', 3),
(17, 'Linked In', NULL, 'social-lk', 'text', 3),
(18, 'Endereço', NULL, 'endereco', 'text', 4),
(19, 'CEP', NULL, 'cep', 'text', 4);

-- --------------------------------------------------------

--
-- Estrutura da tabela `config_cat`
--

CREATE TABLE `config_cat` (
  `id_cat` int(11) NOT NULL,
  `nome` varchar(80) NOT NULL,
  `slug` varchar(30) NOT NULL,
  `icon` varchar(80) NOT NULL DEFAULT ' fa-gear',
  `ordem` int(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `config_cat`
--

INSERT INTO `config_cat` (`id_cat`, `nome`, `slug`, `icon`, `ordem`) VALUES
(1, 'Configurações Básicas', 'basico', 'fa-wrench', 1),
(2, 'Configurações de E-mails', 'emails', 'fa-envelope', 3),
(3, 'Redes Sociais', 'social', 'fa-share-alt', 4),
(4, 'Configurações de Localização', 'local', 'fa-map-marker', 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `email`
--

CREATE TABLE `email` (
  `id_email` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nome` varchar(80) COLLATE utf8_bin NOT NULL,
  `email` varchar(80) COLLATE utf8_bin NOT NULL,
  `para` varchar(80) COLLATE utf8_bin NOT NULL,
  `categoria` enum('contato','orcamento','trabalhe','news','workshop') COLLATE utf8_bin NOT NULL,
  `mensagem` mediumtext COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Extraindo dados da tabela `email`
--

INSERT INTO `email` (`id_email`, `data`, `nome`, `email`, `para`, `categoria`, `mensagem`) VALUES
(1, '2017-05-28 01:06:32', 'Teste de contato', 'teste@artmaker.com.br', 'contato@artmaker.com.br', 'contato', '<h2>Formulário de Contato</h2>\r\n<strong>Nome:</strong>Teste<br/><strong>Email:</strong>teste@artmaker.com.br<br/><strong>Telefone:</strong> (15) 9999-9999<br/><strong>Cidade:</strong> Sorocaba - SP<br/><strong>Mensagem:</strong> Lorem Ipsum<br/>');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pagina`
--

CREATE TABLE `pagina` (
  `id_pagina` int(11) UNSIGNED NOT NULL,
  `nome` varchar(80) NOT NULL,
  `slug` varchar(20) NOT NULL,
  `icon` varchar(20) NOT NULL,
  `ordem` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `pagina`
--

INSERT INTO `pagina` (`id_pagina`, `nome`, `slug`, `icon`, `ordem`) VALUES
(1, 'Banners', 'banners', 'fa-tv', 1),
(3, 'E-mails', 'emails', 'fa-envelope', 4),
(4, 'Usuários', 'usuarios', 'fa-users', 5),
(6, 'Configurações', 'configuracoes', 'fa-cog', 6),
(7, 'Publicações', 'posts', 'fa-newspaper-o', 2),
(8, 'Categorias', 'categorias', 'fa-tags', 3);

-- --------------------------------------------------------

--
-- Estrutura da tabela `permissao`
--

CREATE TABLE `permissao` (
  `id_permissao` int(11) UNSIGNED NOT NULL,
  `fk_pagina` int(11) UNSIGNED NOT NULL,
  `descricao` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `permissao`
--

INSERT INTO `permissao` (`id_permissao`, `fk_pagina`, `descricao`) VALUES
(1, 1, 'banner_view'),
(2, 1, 'banner_edit'),
(3, 1, 'banner_delete'),
(4, 1, 'banner_create'),
(5, 4, 'user_view'),
(6, 4, 'user_edit'),
(7, 4, 'user_delete'),
(8, 4, 'user_create'),
(9, 6, 'config_view'),
(10, 6, 'config_edit'),
(11, 6, 'config_delete'),
(12, 6, 'config_create'),
(13, 3, 'mail_view'),
(14, 3, 'mail_edit'),
(15, 3, 'mail_delete'),
(16, 3, 'mail_create');

-- --------------------------------------------------------

--
-- Estrutura da tabela `posts`
--

CREATE TABLE `posts` (
  `id_posts` int(11) NOT NULL,
  `ativo` enum('s','n') NOT NULL DEFAULT 's',
  `id_categoria` int(11) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `conteudo` text,
  `file` varchar(255) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `posts`
--

INSERT INTO `posts` (`id_posts`, `ativo`, `id_categoria`, `slug`, `titulo`, `desc`, `conteudo`, `file`, `data`) VALUES
(9, 's', 4, 'primeira-publicacao', 'Primeira publicação', 'Lorem ipsum dolor sit amet', '<p>Potenti scelerisque scelerisque praesent cum sed venenatis vestibulum adipiscing a sapien velit tincidunt tristique nec a. Commodo accumsan a vestibulum curabitur condimentum in parturient vel malesuada velit vestibulum ligula ac penatibus consectetur est per ullamcorper mollis vestibulum leo vehicula. Blandit volutpat gravida vestibulum a parturient suspendisse facilisis suspendisse mollis parturient dis euismod a luctus sem odio sagittis.</p>\r\n<p>Morbi cursus vestibulum sociis vel malesuada blandit orci vestibulum rutrum tellus ut sapien et volutpat ac. Senectus a morbi urna id a eu arcu inceptos hendrerit donec vivamus mi in habitasse commodo nam a. Egestas sodales per parturient vestibulum a penatibus parturient a egestas adipiscing a est risus euismod dui ad non fringilla mus a a cum ut a lorem a.</p>', 'download.jpg', '2017-07-16 00:08:45'),
(17, 's', 5, 'segunda-publicacao', 'Segunda publicação', 'Lorem ipsum dolor sit amet', '<p>Cum turpis a hendrerit maecenas dui vivamus nisl ac justo parturient hendrerit in parturient ac at id a at. Sociis ut nec a faucibus gravida est vestibulum sociosqu praesent mus vel id neque vestibulum leo erat. Natoque vivamus risus duis vel donec curae augue dictumst vestibulum nulla a ut facilisi et condimentum magnis placerat mi ad dui. Pharetra semper cubilia torquent lacinia id adipiscing a curae eleifend malesuada himenaeos scelerisque taciti a.</p>\r\n<p><img style=\"display: block; margin-left: auto; margin-right: auto;\" src=\"http://lorempixel.com/400/400/\" alt=\"\" width=\"400\" height=\"400\" /></p>\r\n<p>Maecenas ac lectus eu aptent lacus a platea dapibus commodo parturient ornare diam mollis eros a lacinia quisque per placerat placerat at. Magna a metus hendrerit ac pharetra sit dignissim amet ridiculus velit est ullamcorper massa adipiscing cubilia scelerisque eleifend a pulvinar et condimentum cum dictumst malesuada vestibulum ultricies. Scelerisque a neque est accumsan vivamus interdum ut at rutrum morbi interdum quis maecenas leo a inceptos ultrices tristique at aliquet vitae dolor a adipiscing dui vestibulum.</p>\r\n<p>Scelerisque maecenas litora dapibus bibendum felis bibendum vestibulum mattis parturient fames parturient per sit non egestas faucibus. Consectetur a magna a nec sed eros a et parturient massa amet cum vel morbi scelerisque. Venenatis ullamcorper vestibulum parturient elit habitasse lacinia quam ultrices eget morbi mi a eu at et massa tincidunt a ultrices aenean fames eleifend blandit. Ullamcorper tempor a ut nisi bibendum placerat congue et id curae parturient nunc condimentum vestibulum lacus sociis ut tincidunt elementum condimentum sociis eget. Leo litora amet orci elit ac urna a etiam conubia aliquet quis penatibus mi parturient facilisi suscipit litora hendrerit habitasse pulvinar a vestibulum at condimentum etiam vestibulum vivamus magna.</p>', 'download_02.jpg', '2017-07-16 00:06:56'),
(18, 's', 5, 'terceira-publicacao', 'Terceira publicação', 'Lorem ipsum dolor sit amet', '<p><img style=\"display: block; margin-left: auto; margin-right: auto;\" src=\"http://lorempixel.com/400/200/\" alt=\"\" width=\"400\" height=\"200\" /></p>\r\n<p>Sit duis per scelerisque mi hac praesent habitant vivamus urna aliquam ultricies phasellus habitasse facilisi. Diam inceptos purus nisi mauris arcu adipiscing ante mauris netus parturient ut nisl bibendum a. Suspendisse arcu in scelerisque ac a scelerisque elementum penatibus et nam suspendisse consectetur justo ac porta ultricies primis ornare augue eget vel scelerisque suspendisse nam urna magnis. Rhoncus ad a eu augue class auctor a ut commodo facilisis a at ridiculus libero elementum ante vestibulum a iaculis phasellus dapibus ullamcorper a a. Bibendum potenti suscipit massa condimentum urna ac a mi ut vestibulum eros curae nisi erat imperdiet consectetur nibh conubia facilisi sociosqu bibendum a at.&nbsp;</p>\r\n<p>Lobortis lacus volutpat ipsum eu placerat ultricies eu a vulputate leo dignissim a ullamcorper vestibulum adipiscing nisi fermentum blandit etiam a torquent felis a curabitur scelerisque. Scelerisque ullamcorper habitasse diam nam a vestibulum at aliquam nullam ac a massa nulla fermentum sit mus a cubilia in nunc at morbi et etiam aptent. Pulvinar habitasse curabitur vel parturient nam gravida per a suspendisse praesent parturient vitae natoque elementum dictumst.</p>', 'download_03.jpg', '2017-07-16 00:11:07'),
(19, 's', 7, 'quarta-publicacao', 'Quarta publicação', 'Lorem ipsum dolor sit amet', '<p>At maecenas eget tortor lacinia at ridiculus neque mi scelerisque facilisi morbi sed natoque euismod eu scelerisque velit inceptos a parturient class parturient at quam phasellus. A eleifend nec in sagittis curae at in rutrum nisi id scelerisque ac a lorem suspendisse a nam vestibulum vestibulum. Himenaeos sociosqu a pharetra venenatis accumsan a elementum odio a metus quisque augue elit tellus laoreet sem ultricies. Amet arcu pretium lacinia condimentum ultrices elit nascetur lobortis a massa lobortis ac vitae convallis parturient imperdiet suspendisse suspendisse tincidunt magnis. Erat a enim nisl per inceptos viverra vestibulum scelerisque eros suscipit nisi sit posuere ultricies mus aliquet et iaculis adipiscing suspendisse volutpat a ante per.</p>', 'download_04.jpg', '2017-07-16 00:11:02');

-- --------------------------------------------------------

--
-- Estrutura da tabela `posts_cat`
--

CREATE TABLE `posts_cat` (
  `id_categoria` int(11) NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `cor` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `posts_cat`
--

INSERT INTO `posts_cat` (`id_categoria`, `titulo`, `slug`, `cor`) VALUES
(4, 'Categoria 01', 'categoria-01', '#f2ff00'),
(5, 'Categoria 02', 'cat-02', '#ffc600'),
(6, 'Categoria 03', 'cat-03', '#00a651'),
(7, 'Categoria 04', 'cat-04', '#448ccb'),
(8, 'Categoria 05', 'cat-05', '#959595');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `nome` varchar(80) COLLATE utf8_bin NOT NULL,
  `username` varchar(80) COLLATE utf8_bin NOT NULL,
  `email` varchar(64) COLLATE utf8_bin NOT NULL,
  `datanasc` date NOT NULL,
  `senha` varchar(64) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome`, `username`, `email`, `datanasc`, `senha`) VALUES
(1, 'Administrador', 'admin', 'programador@21brz.com.br', '0000-00-00', '$2y$10$Dr3UXidC7K5FTlj4882lKuWaL5g.uAApJmzzBqaQoeJHRQf0a9KRu');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario_perm`
--

CREATE TABLE `usuario_perm` (
  `fk_usuario` int(11) UNSIGNED NOT NULL,
  `fk_permissao` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `usuario_perm`
--

INSERT INTO `usuario_perm` (`fk_usuario`, `fk_permissao`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id_banner`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_config_config_cat` (`category`);

--
-- Indexes for table `config_cat`
--
ALTER TABLE `config_cat`
  ADD PRIMARY KEY (`id_cat`);

--
-- Indexes for table `email`
--
ALTER TABLE `email`
  ADD PRIMARY KEY (`id_email`);

--
-- Indexes for table `pagina`
--
ALTER TABLE `pagina`
  ADD PRIMARY KEY (`id_pagina`);

--
-- Indexes for table `permissao`
--
ALTER TABLE `permissao`
  ADD PRIMARY KEY (`id_permissao`),
  ADD KEY `FK_permissao_pagina` (`fk_pagina`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id_posts`);

--
-- Indexes for table `posts_cat`
--
ALTER TABLE `posts_cat`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indexes for table `usuario_perm`
--
ALTER TABLE `usuario_perm`
  ADD KEY `fk_usuario` (`fk_usuario`),
  ADD KEY `fk_permissao` (`fk_permissao`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `id_banner` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `config_cat`
--
ALTER TABLE `config_cat`
  MODIFY `id_cat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `email`
--
ALTER TABLE `email`
  MODIFY `id_email` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pagina`
--
ALTER TABLE `pagina`
  MODIFY `id_pagina` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `permissao`
--
ALTER TABLE `permissao`
  MODIFY `id_permissao` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id_posts` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `posts_cat`
--
ALTER TABLE `posts_cat`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `config`
--
ALTER TABLE `config`
  ADD CONSTRAINT `FK_config_config_cat` FOREIGN KEY (`category`) REFERENCES `config_cat` (`id_cat`);

--
-- Limitadores para a tabela `permissao`
--
ALTER TABLE `permissao`
  ADD CONSTRAINT `FK_permissao_pagina` FOREIGN KEY (`fk_pagina`) REFERENCES `pagina` (`id_pagina`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limitadores para a tabela `usuario_perm`
--
ALTER TABLE `usuario_perm`
  ADD CONSTRAINT `fk_permissao` FOREIGN KEY (`fk_permissao`) REFERENCES `permissao` (`id_permissao`),
  ADD CONSTRAINT `fk_usuario` FOREIGN KEY (`fk_usuario`) REFERENCES `usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
