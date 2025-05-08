<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoundWave - Music Streaming Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #121212;
            color: #ffffff;
        }
        
        .player-container {
            height: calc(100vh - 80px);
        }
        
        .waveform {
            background: linear-gradient(90deg, #ff5500, #ff9500);
            height: 60px;
            border-radius: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .waveform::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(
                90deg,
                transparent,
                transparent 2px,
                rgba(255, 255, 255, 0.2) 2px,
                rgba(255, 255, 255, 0.2) 4px
            );
            animation: wave 1s linear infinite;
        }
        
        @keyframes wave {
            0% {
                transform: translateX(-4px);
            }
            100% {
                transform: translateX(0);
            }
        }
        
        .progress-bar {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 0;
            background-color: rgba(255, 255, 255, 0.3);
            z-index: 1;
        }
        
        .modal {
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
        }
        
        .modal.active {
            opacity: 1;
            visibility: visible;
        }
        
        .song-card:hover .play-overlay {
            opacity: 1;
        }
        
        .play-overlay {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Auth Modal -->
    <div id="authModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 modal">
        <div class="bg-gray-800 rounded-lg p-8 w-full max-w-md">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold" id="authModalTitle">Login</h2>
                <button onclick="toggleAuthModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="loginForm">
                <div class="mb-4">
                    <label class="block text-gray-400 mb-2">Email</label>
                    <input type="email" id="loginEmail" class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-400 mb-2">Password</label>
                    <input type="password" id="loginPassword" class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <button onclick="login()" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 rounded font-medium mb-4">Login</button>
                <p class="text-center text-gray-400">Don't have an account? <button onclick="showRegisterForm()" class="text-orange-500 hover:underline">Register</button></p>
            </div>
            
            <div id="registerForm" class="hidden">
                <div class="mb-4">
                    <label class="block text-gray-400 mb-2">Username</label>
                    <input type="text" id="registerUsername" class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-400 mb-2">Email</label>
                    <input type="email" id="registerEmail" class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-400 mb-2">Password</label>
                    <input type="password" id="registerPassword" class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <button onclick="register()" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 rounded font-medium mb-4">Register</button>
                <p class="text-center text-gray-400">Already have an account? <button onclick="showLoginForm()" class="text-orange-500 hover:underline">Login</button></p>
            </div>
        </div>
    </div>
    
    <!-- Add Song Modal -->
    <div id="addSongModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 modal">
        <div class="bg-gray-800 rounded-lg p-8 w-full max-w-md">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Add New Song</h2>
                <button onclick="toggleAddSongModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-400 mb-2">Title</label>
                <input type="text" id="songTitle" class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <div class="mb-4">
                <label class="block text-gray-400 mb-2">Artist</label>
                <input type="text" id="songArtist" class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <div class="mb-4">
                <label class="block text-gray-400 mb-2">Album</label>
                <input type="text" id="songAlbum" class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <div class="mb-4">
                <label class="block text-gray-400 mb-2">Cover Image URL</label>
                <input type="text" id="songCover" class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <div class="mb-6">
                <label class="block text-gray-400 mb-2">Audio File URL</label>
                <input type="text" id="songAudio" class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <button onclick="addSong()" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 rounded font-medium">Add Song</button>
        </div>
    </div>
    
    <!-- Create Playlist Modal -->
    <div id="createPlaylistModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 modal">
        <div class="bg-gray-800 rounded-lg p-8 w-full max-w-md">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Create Playlist</h2>
                <button onclick="toggleCreatePlaylistModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-400 mb-2">Playlist Name</label>
                <input type="text" id="playlistName" class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <div class="mb-6">
                <label class="block text-gray-400 mb-2">Description</label>
                <textarea id="playlistDescription" class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
            </div>
            <button onclick="createPlaylist()" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 rounded font-medium">Create Playlist</button>
        </div>
    </div>
    
    <!-- Main Layout -->
    <div class="flex flex-col h-screen">
        <!-- Header -->
        <header class="bg-gray-900 py-4 px-6 flex items-center justify-between border-b border-gray-800">
            <div class="flex items-center">
                <a href="#" class="text-2xl font-bold text-orange-500 mr-10">SoundWave</a>
                <nav class="hidden md:flex space-x-6">
                    <a href="#" class="text-white hover:text-orange-500">Home</a>
                    <a href="#" class="text-gray-400 hover:text-white">Explore</a>
                    <a href="#" class="text-gray-400 hover:text-white">Library</a>
                    <a href="#" class="text-gray-400 hover:text-white">Charts</a>
                </nav>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="relative hidden md:block">
                    <input type="text" placeholder="Search songs, artists..." class="bg-gray-800 rounded-full px-4 py-2 pl-10 w-64 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                
                <div id="authButtons" class="flex space-x-2">
                    <button onclick="toggleAuthModal()" class="bg-transparent border border-gray-600 text-white px-4 py-1 rounded-full hover:bg-gray-800">Sign up</button>
                    <button onclick="showLoginForm(); toggleAuthModal()" class="bg-orange-500 text-white px-4 py-1 rounded-full hover:bg-orange-600">Login</button>
                </div>
                
                <div id="userMenu" class="hidden items-center space-x-2">
                    <img id="userAvatar" src="https://via.placeholder.com/40" alt="User" class="w-8 h-8 rounded-full">
                    <span id="usernameDisplay" class="text-white"></span>
                    <button onclick="logout()" class="text-gray-400 hover:text-white ml-2">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                    <button onclick="toggleAddSongModal()" id="adminAddSong" class="hidden bg-orange-500 text-white px-3 py-1 rounded-full text-sm hover:bg-orange-600 ml-2">
                        <i class="fas fa-plus mr-1"></i> Add Song
                    </button>
                </div>
            </div>
        </header>
        
        <!-- Mobile Menu -->
        <div class="md:hidden bg-gray-900 p-4 border-b border-gray-800">
            <div class="relative">
                <input type="text" placeholder="Search songs, artists..." class="bg-gray-800 rounded-full px-4 py-2 pl-10 w-full focus:outline-none focus:ring-2 focus:ring-orange-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            <div class="flex justify-around mt-4">
                <a href="#" class="text-white"><i class="fas fa-home"></i></a>
                <a href="#" class="text-gray-400"><i class="fas fa-compass"></i></a>
                <a href="#" class="text-gray-400"><i class="fas fa-music"></i></a>
                <a href="#" class="text-gray-400"><i class="fas fa-chart-line"></i></a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar -->
            <aside class="hidden md:block w-64 bg-gray-900 border-r border-gray-800 overflow-y-auto">
                <div class="p-4">
                    <h3 class="text-gray-400 uppercase text-xs font-bold mb-4">Library</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="flex items-center text-white hover:text-orange-500"><i class="fas fa-heart mr-3"></i> Liked Songs</a></li>
                        <li><a href="#" class="flex items-center text-gray-400 hover:text-white"><i class="fas fa-clock mr-3"></i> Recently Played</a></li>
                        <li><a href="#" class="flex items-center text-gray-400 hover:text-white"><i class="fas fa-download mr-3"></i> Downloads</a></li>
                    </ul>
                    
                    <div class="mt-8">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-gray-400 uppercase text-xs font-bold">Playlists</h3>
                            <button onclick="toggleCreatePlaylistModal()" class="text-gray-400 hover:text-white">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <ul id="playlistsList" class="space-y-2">
                            <li><a href="#" class="flex items-center text-gray-400 hover:text-white"><i class="fas fa-list-ul mr-3"></i> My Favorites</a></li>
                            <li><a href="#" class="flex items-center text-gray-400 hover:text-white"><i class="fas fa-list-ul mr-3"></i> Workout Mix</a></li>
                        </ul>
                    </div>
                </div>
            </aside>
            
            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-900">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-6">Discover New Music</h1>
                    
                    <!-- Featured Playlists -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Featured Playlists</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                            <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-700 transition cursor-pointer">
                                <div class="relative mb-3">
                                    <img src="https://via.placeholder.com/150" alt="Playlist" class="w-full rounded">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 bg-black bg-opacity-50 transition">
                                        <button class="bg-orange-500 text-white p-2 rounded-full">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                </div>
                                <h3 class="font-medium">Chill Vibes</h3>
                                <p class="text-gray-400 text-sm">120 songs</p>
                            </div>
                            <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-700 transition cursor-pointer">
                                <div class="relative mb-3">
                                    <img src="https://via.placeholder.com/150" alt="Playlist" class="w-full rounded">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 bg-black bg-opacity-50 transition">
                                        <button class="bg-orange-500 text-white p-2 rounded-full">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                </div>
                                <h3 class="font-medium">Workout Mix</h3>
                                <p class="text-gray-400 text-sm">85 songs</p>
                            </div>
                            <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-700 transition cursor-pointer">
                                <div class="relative mb-3">
                                    <img src="https://via.placeholder.com/150" alt="Playlist" class="w-full rounded">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 bg-black bg-opacity-50 transition">
                                        <button class="bg-orange-500 text-white p-2 rounded-full">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                </div>
                                <h3 class="font-medium">Focus Flow</h3>
                                <p class="text-gray-400 text-sm">60 songs</p>
                            </div>
                            <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-700 transition cursor-pointer">
                                <div class="relative mb-3">
                                    <img src="https://via.placeholder.com/150" alt="Playlist" class="w-full rounded">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 bg-black bg-opacity-50 transition">
                                        <button class="bg-orange-500 text-white p-2 rounded-full">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                </div>
                                <h3 class="font-medium">Party Hits</h3>
                                <p class="text-gray-400 text-sm">150 songs</p>
                            </div>
                            <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-700 transition cursor-pointer">
                                <div class="relative mb-3">
                                    <img src="https://via.placeholder.com/150" alt="Playlist" class="w-full rounded">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 bg-black bg-opacity-50 transition">
                                        <button class="bg-orange-500 text-white p-2 rounded-full">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                </div>
                                <h3 class="font-medium">Sleep Sounds</h3>
                                <p class="text-gray-400 text-sm">45 songs</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recently Played -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Recently Played</h2>
                        <div class="bg-gray-800 rounded-lg overflow-hidden">
                            <table class="w-full">
                                <thead class="border-b border-gray-700">
                                    <tr class="text-left text-gray-400">
                                        <th class="p-4 w-12">#</th>
                                        <th class="p-4">Title</th>
                                        <th class="p-4 hidden md:table-cell">Artist</th>
                                        <th class="p-4 hidden lg:table-cell">Album</th>
                                        <th class="p-4 w-20">Time</th>
                                        <th class="p-4 w-12"></th>
                                    </tr>
                                </thead>
                                <tbody id="songsList">
                                    <!-- Songs will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Popular Albums -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Popular Albums</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                            <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-700 transition cursor-pointer">
                                <div class="relative mb-3">
                                    <img src="https://via.placeholder.com/150" alt="Album" class="w-full rounded">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 bg-black bg-opacity-50 transition">
                                        <button class="bg-orange-500 text-white p-2 rounded-full">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                </div>
                                <h3 class="font-medium">Midnight Memories</h3>
                                <p class="text-gray-400 text-sm">The Band</p>
                            </div>
                            <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-700 transition cursor-pointer">
                                <div class="relative mb-3">
                                    <img src="https://via.placeholder.com/150" alt="Album" class="w-full rounded">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 bg-black bg-opacity-50 transition">
                                        <button class="bg-orange-500 text-white p-2 rounded-full">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                </div>
                                <h3 class="font-medium">Summer Vibes</h3>
                                <p class="text-gray-400 text-sm">DJ Sunshine</p>
                            </div>
                            <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-700 transition cursor-pointer">
                                <div class="relative mb-3">
                                    <img src="https://via.placeholder.com/150" alt="Album" class="w-full rounded">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 bg-black bg-opacity-50 transition">
                                        <button class="bg-orange-500 text-white p-2 rounded-full">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                </div>
                                <h3 class="font-medium">Urban Legends</h3>
                                <p class="text-gray-400 text-sm">Rap Stars</p>
                            </div>
                            <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-700 transition cursor-pointer">
                                <div class="relative mb-3">
                                    <img src="https://via.placeholder.com/150" alt="Album" class="w-full rounded">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 bg-black bg-opacity-50 transition">
                                        <button class="bg-orange-500 text-white p-2 rounded-full">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                </div>
                                <h3 class="font-medium">Classical Masterpieces</h3>
                                <p class="text-gray-400 text-sm">Symphony Orchestra</p>
                            </div>
                            <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-700 transition cursor-pointer">
                                <div class="relative mb-3">
                                    <img src="https://via.placeholder.com/150" alt="Album" class="w-full rounded">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 bg-black bg-opacity-50 transition">
                                        <button class="bg-orange-500 text-white p-2 rounded-full">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                </div>
                                <h3 class="font-medium">Jazz Nights</h3>
                                <p class="text-gray-400 text-sm">Smooth Jazz Band</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        
        <!-- Player -->
        <div class="bg-gray-800 border-t border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center w-1/4">
                    <img id="nowPlayingCover" src="https://via.placeholder.com/60" alt="Now Playing" class="w-12 h-12 rounded mr-3">
                    <div>
                        <div id="nowPlayingTitle" class="font-medium text-sm truncate">Not Playing</div>
                        <div id="nowPlayingArtist" class="text-gray-400 text-xs">Select a song</div>
                    </div>
                    <button id="likeButton" class="ml-4 text-gray-400 hover:text-white">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
                
                <div class="w-2/4">
                    <div class="flex items-center justify-center space-x-6 mb-2">
                        <button class="text-gray-400 hover:text-white">
                            <i class="fas fa-random"></i>
                        </button>
                        <button class="text-gray-400 hover:text-white">
                            <i class="fas fa-step-backward"></i>
                        </button>
                        <button id="playButton" class="bg-white text-black rounded-full w-8 h-8 flex items-center justify-center hover:bg-gray-200">
                            <i class="fas fa-play"></i>
                        </button>
                        <button class="text-gray-400 hover:text-white">
                            <i class="fas fa-step-forward"></i>
                        </button>
                        <button class="text-gray-400 hover:text-white">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                    <div class="flex items-center">
                        <span id="currentTime" class="text-xs text-gray-400 mr-2">0:00</span>
                        <div class="flex-1 waveform">
                            <div class="progress-bar" id="progressBar"></div>
                        </div>
                        <span id="duration" class="text-xs text-gray-400 ml-2">0:00</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-end w-1/4 space-x-3">
                    <button class="text-gray-400 hover:text-white">
                        <i class="fas fa-list-ul"></i>
                    </button>
                    <button class="text-gray-400 hover:text-white">
                        <i class="fas fa-laptop"></i>
                    </button>
                    <div class="flex items-center">
                        <button class="text-gray-400 hover:text-white mr-2">
                            <i class="fas fa-volume-up"></i>
                        </button>
                        <input type="range" min="0" max="100" value="80" class="w-20 accent-orange-500">
                    </div>
                </div>
            </div>
            
            <audio id="audioPlayer"></audio>
        </div>
    </div>

    <script>
        // Sample data
        const sampleSongs = [
            { id: 1, title: "Blinding Lights", artist: "The Weeknd", album: "After Hours", cover: "https://via.placeholder.com/60", audio: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3", duration: "3:20", liked: false },
            { id: 2, title: "Save Your Tears", artist: "The Weeknd", album: "After Hours", cover: "https://via.placeholder.com/60", audio: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3", duration: "3:35", liked: true },
            { id: 3, title: "Levitating", artist: "Dua Lipa", album: "Future Nostalgia", cover: "https://via.placeholder.com/60", audio: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3", duration: "3:23", liked: false },
            { id: 4, title: "Stay", artist: "The Kid LAROI, Justin Bieber", album: "F*CK LOVE 3", cover: "https://via.placeholder.com/60", audio: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3", duration: "2:21", liked: false },
            { id: 5, title: "Good 4 U", artist: "Olivia Rodrigo", album: "SOUR", cover: "https://via.placeholder.com/60", audio: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-5.mp3", duration: "2:58", liked: true }
        ];
        
        const samplePlaylists = [
            { id: 1, name: "My Favorites", songs: [1, 2, 5] },
            { id: 2, name: "Workout Mix", songs: [1, 3, 4] }
        ];
        
        // State
        let currentUser = null;
        let isAdmin = false;
        let songs = [...sampleSongs];
        let playlists = [...samplePlaylists];
        let currentSongIndex = -1;
        let isPlaying = false;
        
        // DOM Elements
        const audioPlayer = document.getElementById('audioPlayer');
        const playButton = document.getElementById('playButton');
        const progressBar = document.getElementById('progressBar');
        const currentTimeDisplay = document.getElementById('currentTime');
        const durationDisplay = document.getElementById('duration');
        const nowPlayingTitle = document.getElementById('nowPlayingTitle');
        const nowPlayingArtist = document.getElementById('nowPlayingArtist');
        const nowPlayingCover = document.getElementById('nowPlayingCover');
        const likeButton = document.getElementById('likeButton');
        const songsList = document.getElementById('songsList');
        const playlistsList = document.getElementById('playlistsList');
        const authButtons = document.getElementById('authButtons');
        const userMenu = document.getElementById('userMenu');
        const usernameDisplay = document.getElementById('usernameDisplay');
        const userAvatar = document.getElementById('userAvatar');
        const adminAddSong = document.getElementById('adminAddSong');
        
        // Initialize
        function init() {
            renderSongs();
            renderPlaylists();
            
            // Audio player events
            audioPlayer.addEventListener('timeupdate', updateProgressBar);
            audioPlayer.addEventListener('loadedmetadata', updateDuration);
            audioPlayer.addEventListener('ended', playNextSong);
            
            // Check if user is logged in from localStorage
            const storedUser = localStorage.getItem('currentUser');
            if (storedUser) {
                currentUser = JSON.parse(storedUser);
                isAdmin = currentUser.email === 'admin@soundwave.com';
                updateUserUI();
            }
        }
        
        // Render songs in the table
        function renderSongs() {
            songsList.innerHTML = '';
            
            songs.forEach((song, index) => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-700 cursor-pointer';
                row.dataset.index = index;
                
                row.innerHTML = `
                    <td class="p-4 text-gray-400">${index + 1}</td>
                    <td class="p-4">
                        <div class="flex items-center">
                            <img src="${song.cover}" alt="${song.title}" class="w-10 h-10 rounded mr-3">
                            <div>
                                <div class="font-medium">${song.title}</div>
                                <div class="text-gray-400 text-sm">${song.artist}</div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 hidden md:table-cell text-gray-400">${song.artist}</td>
                    <td class="p-4 hidden lg:table-cell text-gray-400">${song.album}</td>
                    <td class="p-4 text-gray-400">${song.duration}</td>
                    <td class="p-4 text-gray-400">
                        <i class="fas fa-ellipsis-h hover:text-white"></i>
                    </td>
                `;
                
                row.addEventListener('click', () => playSong(index));
                songsList.appendChild(row);
            });
        }
        
        // Render playlists in sidebar
        function renderPlaylists() {
            playlistsList.innerHTML = '';
            
            playlists.forEach(playlist => {
                const item = document.createElement('li');
                item.innerHTML = `
                    <a href="#" class="flex items-center text-gray-400 hover:text-white">
                        <i class="fas fa-list-ul mr-3"></i> ${playlist.name}
                    </a>
                `;
                playlistsList.appendChild(item);
            });
        }
        
        // Play song
        function playSong(index) {
            if (index < 0 || index >= songs.length) return;
            
            currentSongIndex = index;
            const song = songs[currentSongIndex];
            
            audioPlayer.src = song.audio;
            audioPlayer.play();
            isPlaying = true;
            
            // Update UI
            nowPlayingTitle.textContent = song.title;
            nowPlayingArtist.textContent = song.artist;
            nowPlayingCover.src = song.cover;
            playButton.innerHTML = '<i class="fas fa-pause"></i>';
            
            // Update like button
            updateLikeButton();
        }
        
        // Toggle play/pause
        function togglePlayPause() {
            if (currentSongIndex === -1 && songs.length > 0) {
                playSong(0);
                return;
            }
            
            if (isPlaying) {
                audioPlayer.pause();
                playButton.innerHTML = '<i class="fas fa-play"></i>';
            } else {
                audioPlayer.play();
                playButton.innerHTML = '<i class="fas fa-pause"></i>';
            }
            
            isPlaying = !isPlaying;
        }
        
        // Play next song
        function playNextSong() {
            if (currentSongIndex < songs.length - 1) {
                playSong(currentSongIndex + 1);
            } else {
                playSong(0);
            }
        }
        
        // Play previous song
        function playPreviousSong() {
            if (currentSongIndex > 0) {
                playSong(currentSongIndex - 1);
            } else {
                playSong(songs.length - 1);
            }
        }
        
        // Update progress bar
        function updateProgressBar() {
            const { currentTime, duration } = audioPlayer;
            const progressPercent = (currentTime / duration) * 100;
            progressBar.style.width = ${progressPercent}%;
            
            // Update current time display
            currentTimeDisplay.textContent = formatTime(currentTime);
        }
        
        // Update duration display
        function updateDuration() {
            durationDisplay.textContent = formatTime(audioPlayer.duration);
        }
        
        // Format time (seconds to MM:SS)
        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return ${mins}:${secs < 10 ? '0' : ''}${secs};
        }
        
        // Toggle like for current song
        function toggleLike() {
            if (currentSongIndex === -1) return;
            
            songs[currentSongIndex].liked = !songs[currentSongIndex].liked;
            updateLikeButton();
        }
        
        // Update like button UI
        function updateLikeButton() {
            if (currentSongIndex === -1) {
                likeButton.innerHTML = '<i class="far fa-heart"></i>';
                return;
            }
            
            const song = songs[currentSongIndex];
            likeButton.innerHTML = song.liked 
                ? '<i class="fas fa-heart text-orange-500"></i>' 
                : '<i class="far fa-heart"></i>';
        }
        
        // Auth functions
        function login() {
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            // Simple validation
            if (!email || !password) {
                alert('Please enter both email and password');
                return;
            }
            
            // In a real app, you would validate against your backend
            currentUser = {
                username: email.split('@')[0],
                email: email,
                avatar: https://ui-avatars.com/api/?name=${email.split('@')[0]}&background=random
            };
            
            isAdmin = email === 'admin@soundwave.com';
            
            // Save to localStorage
            localStorage.setItem('currentUser', JSON.stringify(currentUser));
            
            // Update UI
            updateUserUI();
            toggleAuthModal();
        }
        
        function register() {
            const username = document.getElementById('registerUsername').value;
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            
            // Simple validation
            if (!username || !email || !password) {
                alert('Please fill all fields');
                return;
            }
            
            // In a real app, you would send this to your backend
            currentUser = {
                username: username,
                email: email,
                avatar: https://ui-avatars.com/api/?name=${username}&background=random
            };
            
            isAdmin = false;
            
            // Save to localStorage
            localStorage.setItem('currentUser', JSON.stringify(currentUser));
            
            // Update UI
            updateUserUI();
            toggleAuthModal();
        }
        
        function logout() {
            currentUser = null;
            isAdmin = false;
            localStorage.removeItem('currentUser');
            
            // Update UI
            authButtons.classList.remove('hidden');
            userMenu.classList.add('hidden');
            adminAddSong.classList.add('hidden');
        }
        
        function updateUserUI() {
            if (currentUser) {
                authButtons.classList.add('hidden');
                userMenu.classList.remove('hidden');
                usernameDisplay.textContent = currentUser.username;
                userAvatar.src = currentUser.avatar;
                
                if (isAdmin) {
                    adminAddSong.classList.remove('hidden');
                } else {
                    adminAddSong.classList.add('hidden');
                }
            } else {
                authButtons.classList.remove('hidden');
                userMenu.classList.add('hidden');
            }
        }
        
        // Modal functions
        function toggleAuthModal() {
            document.getElementById('authModal').classList.toggle('active');
        }
        
        function toggleAddSongModal() {
            document.getElementById('addSongModal').classList.toggle('active');
        }
        
        function toggleCreatePlaylistModal() {
            document.getElementById('createPlaylistModal').classList.toggle('active');
        }
        
        function showLoginForm() {
            document.getElementById('loginForm').classList.remove('hidden');
            document.getElementById('registerForm').classList.add('hidden');
            document.getElementById('authModalTitle').textContent = 'Login';
        }
        
        function showRegisterForm() {
            document.getElementById('loginForm').classList.add('hidden');
            document.getElementById('registerForm').classList.remove('hidden');
            document.getElementById('authModalTitle').textContent = 'Register';
        }
        
        // Admin functions
        function addSong() {
            const title = document.getElementById('songTitle').value;
            const artist = document.getElementById('songArtist').value;
            const album = document.getElementById('songAlbum').value;
            const cover = document.getElementById('songCover').value || 'https://via.placeholder.com/60';
            const audio = document.getElementById('songAudio').value;
            
            if (!title || !artist || !audio) {
                alert('Please fill required fields: Title, Artist and Audio URL');
                return;
            }
            
            const newSong = {
                id: songs.length + 1,
                title,
                artist,
                album: album || 'Single',
                cover,
                audio,
                duration: '3:30', // In a real app, you would calculate this
                liked: false
            };
            
            songs.push(newSong);
            renderSongs();
            toggleAddSongModal();
            
            // Clear form
            document.getElementById('songTitle').value = '';
            document.getElementById('songArtist').value = '';
            document.getElementById('songAlbum').value = '';
            document.getElementById('songCover').value = '';
            document.getElementById('songAudio').value = '';
        }
        
        // Playlist functions
        function createPlaylist() {
            const name = document.getElementById('playlistName').value;
            const description = document.getElementById('playlistDescription').value;
            
            if (!name) {
                alert('Please enter a playlist name');
                return;
            }
            
            const newPlaylist = {
                id: playlists.length + 1,
                name,
                description,
                songs: []
            };
            
            playlists.push(newPlaylist);
            renderPlaylists();
            toggleCreatePlaylistModal();
            
            // Clear form
            document.getElementById('playlistName').value = '';
            document.getElementById('playlistDescription').value = '';
        }
        
        // Event listeners
        playButton.addEventListener('click', togglePlayPause);
        likeButton.addEventListener('click', toggleLike);
        
        // Initialize the app
        window.onload = init;
    </script>
</body>
</html>