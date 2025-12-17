<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloudinary Image Upload - Game Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">Cloudinary Image Upload</h1>
                <p class="text-gray-600">Upload game images to Cloudinary for your game store</p>
            </div>

            <!-- Navigation -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="flex justify-center space-x-4">
                    <a href="/database" class="bg-purple-500 text-white px-6 py-3 rounded-lg hover:bg-purple-600 transition duration-200">
                        🗄️ Database Management
                    </a>
                    <a href="/welcome" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition duration-200">
                        📄 Welcome Page
                    </a>
                </div>
            </div>

            <!-- Upload Options -->
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <!-- Single Image Upload -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Single Image Upload</h2>
                    <form id="singleUploadForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Image</label>
                            <input type="file" id="singleImage" name="image" accept="image/*" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Folder (optional)</label>
                            <input type="text" id="singleFolder" name="folder" placeholder="game-store" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                            Upload Single Image
                        </button>
                    </form>
                    <div id="singleResult" class="mt-4"></div>
                </div>

                <!-- Bulk Upload -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Bulk Upload All Images</h2>
                    <p class="text-sm text-gray-600 mb-4">Upload all images from the public/image folder</p>
                    <form id="bulkUploadForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Folder (optional)</label>
                            <input type="text" id="bulkFolder" name="folder" placeholder="game-store" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition duration-200">
                            Upload All Images
                        </button>
                    </form>
                    <div id="bulkResult" class="mt-4"></div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Upload Results</h2>
                    <button id="loadResults" class="bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 transition duration-200">
                        Load Results
                    </button>
                </div>
                <div id="resultsContainer" class="space-y-4"></div>
            </div>

            <!-- Command Line Instructions -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Command Line Upload</h2>
                <p class="text-sm text-gray-600 mb-4">You can also upload images using the Artisan command:</p>
                <div class="bg-gray-100 p-4 rounded-md font-mono text-sm">
                    <code>php artisan images:upload-to-cloudinary --folder=game-store</code>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Single image upload
        document.getElementById('singleUploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            const imageFile = document.getElementById('singleImage').files[0];
            const folder = document.getElementById('singleFolder').value;
            
            if (!imageFile) {
                showResult('singleResult', 'Please select an image file.', 'error');
                return;
            }
            
            formData.append('image', imageFile);
            if (folder) {
                formData.append('folder', folder);
            }
            
            try {
                const response = await axios.post('/cloudinary/upload', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                
                if (response.data.success) {
                    showResult('singleResult', `Image uploaded successfully! URL: ${response.data.url}`, 'success');
                } else {
                    showResult('singleResult', `Upload failed: ${response.data.message}`, 'error');
                }
            } catch (error) {
                showResult('singleResult', `Error: ${error.response?.data?.message || error.message}`, 'error');
            }
        });

        // Bulk upload
        document.getElementById('bulkUploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const folder = document.getElementById('bulkFolder').value;
            const formData = new FormData();
            
            if (folder) {
                formData.append('folder', folder);
            }
            
            showResult('bulkResult', 'Uploading all images... This may take a while.', 'info');
            
            try {
                const response = await axios.post('/cloudinary/upload-all', formData);
                
                if (response.data.success) {
                    const data = response.data.data;
                    const message = `Upload completed! ${data.total_processed} images processed. ${data.uploaded.length} successful, ${data.failed.length} failed.`;
                    showResult('bulkResult', message, 'success');
                } else {
                    showResult('bulkResult', `Upload failed: ${response.data.message}`, 'error');
                }
            } catch (error) {
                showResult('bulkResult', `Error: ${error.response?.data?.message || error.message}`, 'error');
            }
        });

        // Load results
        document.getElementById('loadResults').addEventListener('click', async function() {
            try {
                const response = await axios.get('/cloudinary/results');
                
                if (response.data.success) {
                    displayResults(response.data.data);
                } else {
                    showResult('resultsContainer', 'No results found.', 'info');
                }
            } catch (error) {
                showResult('resultsContainer', `Error loading results: ${error.response?.data?.message || error.message}`, 'error');
            }
        });

        function showResult(containerId, message, type) {
            const container = document.getElementById(containerId);
            const colors = {
                success: 'bg-green-100 border-green-400 text-green-700',
                error: 'bg-red-100 border-red-400 text-red-700',
                info: 'bg-blue-100 border-blue-400 text-blue-700'
            };
            
            container.innerHTML = `<div class="p-3 border rounded-md ${colors[type]}">${message}</div>`;
        }

        function displayResults(data) {
            const container = document.getElementById('resultsContainer');
            
            let html = `
                <div class="mb-4">
                    <h3 class="font-semibold text-gray-800">Upload Summary</h3>
                    <p class="text-sm text-gray-600">Total processed: ${data.total_processed} | Successful: ${data.uploaded.length} | Failed: ${data.failed.length}</p>
                    <p class="text-sm text-gray-600">Timestamp: ${new Date(data.timestamp).toLocaleString()}</p>
                </div>
            `;
            
            if (data.uploaded.length > 0) {
                html += `
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-800 mb-2">Successfully Uploaded (${data.uploaded.length})</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                `;
                
                data.uploaded.slice(0, 9).forEach(image => {
                    html += `
                        <div class="border rounded-md p-3">
                            <img src="${image.url}" alt="${image.filename}" class="w-full h-32 object-cover rounded mb-2">
                            <p class="text-sm font-medium truncate">${image.filename}</p>
                            <p class="text-xs text-gray-500 truncate">${image.public_id}</p>
                        </div>
                    `;
                });
                
                html += '</div>';
                if (data.uploaded.length > 9) {
                    html += `<p class="text-sm text-gray-600 mt-2">... and ${data.uploaded.length - 9} more images</p>`;
                }
                html += '</div>';
            }
            
            if (data.failed.length > 0) {
                html += `
                    <div>
                        <h4 class="font-medium text-gray-800 mb-2">Failed Uploads (${data.failed.length})</h4>
                        <div class="space-y-2">
                `;
                
                data.failed.forEach(failed => {
                    html += `
                        <div class="bg-red-50 border border-red-200 rounded-md p-3">
                            <p class="text-sm font-medium text-red-800">${failed.filename}</p>
                            <p class="text-xs text-red-600">${failed.error}</p>
                        </div>
                    `;
                });
                
                html += '</div></div>';
            }
            
            container.innerHTML = html;
        }
    </script>
</body>
</html> 