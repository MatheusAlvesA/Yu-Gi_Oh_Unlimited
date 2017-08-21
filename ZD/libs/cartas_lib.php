<?php
// este arquivo funciona como um pluguin que une todas as cartas
include('libs/cards/carta.php');
include('libs/cards/monstro_normal.php');
include('libs/cards/magica.php');
include('libs/cards/armadilha.php');
include('libs/cards/efeitos_lib.php');
include('libs/cards/c_2.php'); // A Hero Emerges # armadilha
include('libs/cards/c_3.php'); // Abaki # monstro efeito
include('libs/cards/c_4.php'); // Abare Ushioni # monstro efeito
include('libs/cards/c_5.php'); // Acid Rain # magica
include('libs/cards/c_7.php'); // Advance Draw # magica
include('libs/cards/c_8.php'); // Against the Wind # magica
include('libs/cards/c_9.php'); // Airknight Parshath # monstro efeito
include('libs/cards/c_16.php'); // Altar for Tribute # armadilha
include('libs/cards/c_22.php'); // Ancient Leaf # magica
include('libs/cards/c_25.php'); // Ancient Rules # magica
include('libs/cards/c_33.php'); // Armed Dragon LV3 # monstro efeito
include('libs/cards/c_34.php'); // Armed Dragon LV5 # monstro efeito
include('libs/cards/c_35.php'); // Armed Dragon LV7 # monstro efeito
include('libs/cards/c_32.php'); // Armed Dragon LV10 #  mostro efeito
include('libs/cards/c_36.php'); // Armed Ninja #  monstro efeito
include('libs/cards/c_40.php'); // Asceticism of the Six Samurai # magica
include('libs/cards/c_41.php'); // At One With the Sword # magica # equipamento
include('libs/cards/c_43.php'); // Attraffic Control # magica
include('libs/cards/c_44.php'); // Axe Dragonute # monstro efeito
include('libs/cards/c_49.php'); // Banner of Courage # magica
include('libs/cards/c_55.php'); // Battlestorm # monstro efeito
include('libs/cards/c_57.php'); // Beast Fangs # equipamento
include('libs/cards/c_58.php'); // Beast King Barbaros # monstro efeito
include('libs/cards/c_59.php'); // Beast Machine King Barbaros Ur # monstro efeito
include('libs/cards/c_61.php'); // Beastly Mirror Ritual # Magica Ritual
include('libs/cards/c_68.php'); // BlackLuster Ritual # Magica Ritual
include('libs/cards/c_69.php'); // Black Luster Soldier # Monstro Ritual
include('libs/cards/c_70.php'); // Black Luster Soldier - Envoy of the Beginning # Monstro Efeito
include('libs/cards/c_71.php'); // Black Magic Ritual # Magica Ritual
include('libs/cards/c_72.php'); // Black Pendant # magica # equipamento
include('libs/cards/c_73.php'); // Black Sonic # Armadilha
include('libs/cards/c_74.php'); // Black Stego # monstro efeito
include('libs/cards/c_75.php'); // Black Veloci # monstro efeito
include('libs/cards/c_76.php'); // Black Whirlwind # magica
include('libs/cards/c_77.php'); // Blackcack # armadilha
include('libs/cards/c_79.php'); // Blackwing - Bora the Spear # monstro efeito
include('libs/cards/c_80.php'); // Blackwing - Fane the Steel Chain # monstro efeito
include('libs/cards/c_82.php'); // Blackwing - Gladius the MidnightSun # monstro efeito
include('libs/cards/c_83.php'); // Blackwing - Gust the BackBlast # monstro efeito
include('libs/cards/c_84.php'); // Blackwing - Shurakwing - Elphin the Raven # monstro efeito
include('libs/cards/c_81.php'); // Blac the Blue Flame # monstro efeito
include('libs/cards/c_85.php'); // Blackwing - Sirocco the Dawn # monstro efeito
include('libs/cards/c_87.php'); // Blasting the Ruins # armadilha
include('libs/cards/c_89.php'); // Blizzard Dragon # monstro efeito
include('libs/cards/c_90.php'); // Blizzed, Defender of the Ice Barrier # monstro efeito
include('libs/cards/c_91.php'); // Block Attack # magica
include('libs/cards/c_93.php'); // Blue Medicine # magica
include('libs/cards/c_98.php'); // Book of Secret Arts # equipamento
include('libs/cards/c_100.php'); // Breath of Light # magica
include('libs/cards/c_101.php'); // Bright Star Dragon # monstro efeito
include('libs/cards/c_103.php'); // Burden of the Mighty # magica
include('libs/cards/c_104.php'); // Buster Blade # monstro efeito
include('libs/cards/c_105.php'); // Call of the Earthbound # armadilha
include('libs/cards/c_106.php'); // Card Of Sanctity # magica
include('libs/cards/c_107.php'); // Card Trader # magica
include('libs/cards/c_108.php'); // Cards for Black Feathers # magica
include('libs/cards/c_109.php'); // Castle Wall # armadilha
include('libs/cards/c_110.php'); // Ceasefire # armadilha
include('libs/cards/c_112.php'); // Cemetery Bomb # armadilha
include('libs/cards/c_113.php'); // Chainsaw Insect # monstro effect
include('libs/cards/c_114.php'); // Chakra # Monstro Ritual
include('libs/cards/c_116.php'); // Chaos Necromancer # Monstro Efeito
include('libs/cards/c_119.php'); // Chorus of Sanctuary # Magica FIELD
include('libs/cards/c_123.php'); // Commencement Dance # Magica Ritual
include('libs/cards/c_124.php'); // Constellar Ocubens # Monstro Efeito
include('libs/cards/c_125.php'); // Constellar Adebaran # Monstro Efeito
include('libs/cards/c_126.php'); // Constellar Agiedi # Monstro Efeito
include('libs/cards/c_127.php'); // Constellar Alrescha # Monstro Efeito
include('libs/cards/c_128.php'); // Constellar Antares # Monstro Efeito
include('libs/cards/c_129.php'); // Constellar Kaus # Monstro Efeito
include('libs/cards/c_130.php'); // Constellar Leonis # Monstro Efeito
include('libs/cards/c_131.php'); // Constellar Meteor # Armadilha
include('libs/cards/c_132.php'); // Constellar Pollux # Monstro Efeito
include('libs/cards/c_133.php'); // Constellar Rasalhague # Monstro Efeito
include('libs/cards/c_134.php'); // Constellar Sheratan # Monstro Efeito
include('libs/cards/c_135.php'); // Constellar Sheratan # Monstro Efeito
include('libs/cards/c_136.php'); // Constellar Sombre # Monstro Efeito
include('libs/cards/c_137.php'); // Constellar Star Cradle # Mágica
include('libs/cards/c_138.php'); // Constellar Virgo # Monstro Efeito
include('libs/cards/c_139.php'); // Constellar Zubeneschamali # Monstro Efeito
include('libs/cards/c_140.php'); // Contract with the Dark Master # Magica Ritual
include('libs/cards/c_141.php'); // Copy Cat # Monstro Efeito
include('libs/cards/c_144.php'); // Cost Down # Mágica
include('libs/cards/c_145.php'); // Crab Turtle # Monstro Ritual
include('libs/cards/c_148.php'); // Crimson Ninja # Monstro Efeito
include('libs/cards/c_149.php'); // Cunning of The Six Samurai # Mágica
include('libs/cards/c_150.php'); // Cup of Ace # Mágica
include('libs/cards/c_152.php'); // Curse of the Masked Beast # Magica Ritual
include('libs/cards/c_153.php'); // Cyber Dragon # Monstro Efeito
include('libs/cards/c_163.php'); // Dark Armed Dragon # Monstro Efeito
include('libs/cards/c_166.php'); // Dark Driceratops # Monstro Efeito
include('libs/cards/c_167.php'); // Dark Elf # Monstro Efeito
include('libs/cards/c_168.php'); // Dark Energy # Mágica equipamento
include('libs/cards/c_169.php'); // Dark Eruption # Mágica
include('libs/cards/c_171.php'); // Dark Hole # Mágica
include('libs/cards/c_173.php'); // Dark Magic Curtain # Mágica
include('libs/cards/c_175.php'); // Dark Magician Girl # Monstro efeito
include('libs/cards/c_176.php'); // Dark Magician of Chaos # Monstro efeito
include('libs/cards/c_177.php'); // Dark Master - Zorc # Monstro Efeito
include('libs/cards/c_178.php'); // Dark Mirror Force # Armadilha
include('libs/cards/c_181.php'); // Dark-Piercing Light # Mágica
include('libs/cards/c_186.php'); // Dash Warrior # Monstro Efeito
include('libs/cards/c_187.php'); // De-Spell # Mágica
include('libs/cards/c_188.php'); // Decoy Dragon # Monstro Efeito
include('libs/cards/c_190.php'); // Demise, King of Armageddon # Monstro Efeito
include('libs/cards/c_191.php'); // Depth Amulet # Armadilha
include('libs/cards/c_192.php'); // Des Kangaroo # Monstro Efeito
include('libs/cards/c_193.php'); // Des Koala # Monstro Efeito
include('libs/cards/c_194.php'); // Desert Sunlight # armadilha
include('libs/cards/c_196.php'); // Destruction Ring # armadilha
include('libs/cards/c_198.php'); // Dian Keto the Cure Master # Mágica
include('libs/cards/c_199.php'); // Different Dimension Capsule # Mágica
include('libs/cards/c_200.php'); // Different Dimension Dragon # Monstro Efeito
include('libs/cards/c_201.php'); // Dimensional Prison # armadilha
include('libs/cards/c_204.php'); // Divine Dragon Apocralyph # Monstro Efeito
include('libs/cards/c_206.php'); // Dokurorider # Monstro Ritual
include('libs/cards/c_209.php'); // Double Summon # Mágica
include('libs/cards/c_210.php'); // Double-Edged Sword Technique # armadilha
include('libs/cards/c_211.php'); // Dragon Capture Jar # armadilha
include('libs/cards/c_212.php'); // Dragon Treasure # Mágica #equipamento
include('libs/cards/c_215.php'); // Dragons Mirror # Mágica
include('libs/cards/c_216.php'); // Dragons Rebirth # armadilha
include('libs/cards/c_217.php'); // Draining Shield # armadilha
include('libs/cards/c_221.php'); // Dust Tornado # armadilha
include('libs/cards/c_222.php'); // Earth Armor Ninja # Monstro Efeito
include('libs/cards/c_224.php'); // Earthquake # Mágica
include('libs/cards/c_225.php'); // Elder or the Six Samurai # Monstro Efeito
include('libs/cards/c_226.php'); // Electro-Whip # Mágica Equipamento
include('libs/cards/c_234.php'); // Elemental HERO Woodsman # Monstro Efeito
include('libs/cards/c_235.php'); // Elfs Light # Mágica Equipamento
include('libs/cards/c_238.php'); // End of the World # Magica Ritual
include('libs/cards/c_239.php'); // Enishi, Shiens Chancellor # Monstro Efeito
include('libs/cards/c_240.php'); // Enraged Muka Muka # Monstro Efeito
include('libs/cards/c_241.php'); // Exodia the Forbidden One # Monstro Efeito
include('libs/cards/c_242.php'); // Exploder Dragon # Monstro Efeito
include('libs/cards/c_247.php'); // Feedback Warrior # Monstro Efeito
include('libs/cards/c_251.php'); // Fiends Mirror # Monstro Ritual
include('libs/cards/c_253.php'); // Final Flame # Mágica
include('libs/cards/c_254.php'); // Resurrection of Chakra # Magica Ritual
include('libs/cards/c_255.php'); // Fire Darts # armadilha
include('libs/cards/c_260.php'); // Fissure # Mágica
include('libs/cards/c_273.php'); // Follow Wind # Mágica equipamento
include('libs/cards/c_274.php'); // Foolish Burial # Mágica
include('libs/cards/c_275.php'); // Fortress Whale # Monstro Ritual
include('libs/cards/c_276.php'); // Forest # Mágica campo
include('libs/cards/c_277.php'); // Fortress Whales Oath # Mágica Ritual
include('libs/cards/c_278.php'); // Fossil Dig # Mágica
include('libs/cards/c_282.php'); // Full Salvo # armadilha
include('libs/cards/c_283.php'); // Fusion Sage # Mágica
include('libs/cards/c_287.php'); // Gaia Power # Mágica campo
include('libs/cards/c_291.php'); // Garma Sword # Monstro Ritual
include('libs/cards/c_292.php'); // Garma Sword Oath # Magica Ritual
// FALTANDO
include('libs/cards/c_319.php'); // Gift of The Mystical Elf # Mágica
include('libs/cards/c_328.php'); // Goblin Thief # Mágica
include('libs/cards/c_329.php'); // Goblins Secret Remedy # Mágica

include('libs/cards/c_454.php'); // Magician of Black Chaos # Monstro Ritual
include('libs/cards/c_555.php'); // Performance of Sword # Monstro Ritual
include('libs/cards/c_561.php'); // Polimerization # Magica
include('libs/cards/c_602.php'); // Reshef the Dark Being # Monstro Ritual
include('libs/cards/c_604.php'); // Resurrection of Chakra # Magica Ritual
include('libs/cards/c_607.php'); // Revival of Dokurorider # Magica Ritual
include('libs/cards/c_699.php'); // Swift gaia the fierce knight # Monstro efeito
include('libs/cards/c_722.php'); // The Masked Beast # Monstro Ritual
include('libs/cards/c_760.php'); // Turtle Oath # Magica Ritual
?>