<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantheon Site Create</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-200 min-h-screen flex items-center justify-center">
    @vite(['resources/js/app.js'])

    <div class="bg-white p-8 rounded shadow-md w-full sm:w-96">
        <h2 class="text-2xl font-bold mb-4">Create Pantheon Site</h2>
        <form id="createSiteForm" method="post" action="{{ route('site.create') }}">
            @csrf
            <label for="site_label" class="block mb-2">Site Label:</label>
            <input type="text" id="site_label" name="site_label" required
                   class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-500">

            <label for="cms" class="block mt-4 mb-2">CMS:</label>
            <select id="cms" name="cms" onchange="updateUpstreamId(this.value)" required
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-500">
                <option value="">Select CMS</option>
                <option value="drupal7">Drupal 7</option>
                <option value="drupal9">Drupal 9</option>
                <option value="drupal10">Drupal 10</option>
                <option value="wordpress">WordPress</option>
            </select>

            <label for="supporting_org" class="block mt-4 mb-2">Supporting Org:</label>
            <select id="supporting_org" name="supporting_org" required
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-500">
                <option value="">Select Supporting Org</option>
                <option value="tyler-technologies-ne">tyler-technologies-ne</option>
                <option value="tyler-technologies-hi">tyler-technologies-hi</option>
                <option value="tyler-technologies-al">tyler-technologies-al</option>
                <option value="tyler-technologies-la">tyler-technologies-la</option>
                <option value="tyler-technologies-ky">tyler-technologies-ky</option>
            </select>

            <label for="site_plan" class="block mt-4 mb-2">Site Plan:</label>
            <select id="site_plan" name="site_plan" required
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-500">
                <option value="">Select Site Plan</option>
                <option value="plan-basic_small-contract-annual-1">Basic</option>
                <option value="plan-performance_small-contract-annual-1">Performance Small</option>
                <option value="plan-performance_medium-contract-annual-1">Performance Medium</option>
                <option value="plan-performance_large-contract-annual-1">Performance Large</option>
                <option value="plan-performance_xlarge-contract-annual-1">Performance Extra Large</option>
            </select>

            <label for="tag" class="block mt-4 mb-2">Tag:</label>
            <select id="tag" name="tag" required
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-500">
                <option value="">Select Tag</option>
                <option value="Alabama">Alabama</option>
                <option value="Hawaii">Hawaii</option>
                <option value="Nebraska">Nebraska</option>
                <option value="Pantheon">Pantheon</option>
                <option value="Louisiana">Louisiana</option>
            </select>

            <!-- Hidden upstream_id field -->
            <input type="hidden" id="upstream_id" name="upstream_id">

            <input type="submit" value="Create Site" class="mt-6 px-4 py-2 bg-blue-500 text-white rounded-md cursor-pointer">
        </form>

        <!-- Status display area -->
        <div id="statusArea" class="mt-8 hidden">
            <h3 class="text-lg font-bold mb-2">Create Site Status:</h3>
            <ul id="statusList" class="pl-4">
                <!-- Status messages will be dynamically added here -->
            </ul>
        </div>
    </div>

    <script>
        function updateUpstreamId(cms) {
            const upstreamIdField = document.getElementById('upstream_id');

            switch (cms) {
                case 'drupal7':
                    upstreamIdField.value = '21e1fada-199c-492b-97bd-0b36b53a9da0';
                    break;
                case 'drupal9':
                    upstreamIdField.value = '8a129104-9d37-4082-aaf8-e6f31154644e';
                    break;
                case 'drupal10':
                    upstreamIdField.value = 'bde48795-b16d-443f-af01-8b1790caa1af';
                    break;
                case 'wordpress':
                    upstreamIdField.value = 'e8fe8550-1ab9-4964-8838-2b9abdccf4bf';
                    break;
                default:
                    upstreamIdField.value = '';
                    break;
            }
        }

        document.getElementById('createSiteForm').addEventListener('submit', (event) => {
            event.preventDefault(); // Prevent the default form submission

            const formData = new FormData(event.target); // Create a FormData object

            // Show the status area
            document.getElementById('statusArea').classList.remove('hidden');

            // Send the form data using fetch
            fetch(event.target.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value // Include CSRF token
                }
            })
            .then(response => response.json())
            .then(data => {
                // Handle success or error messages
                const statusList = document.getElementById('statusList');
                const listItem = document.createElement('li');
                listItem.textContent = data.message; // Assuming your server returns a message
                statusList.appendChild(listItem);
            })
            .catch(error => {
                console.error('Error:', error);
                const statusList = document.getElementById('statusList');
                const listItem = document.createElement('li');
                listItem.textContent = 'An error occurred. Please try again.';
                statusList.appendChild(listItem);
            });
        });
        //document.getElementById('createSiteForm').addEventListener('submit', (event) => {
          //  document.getElementById('statusArea').classList.remove('hidden'); // Show the status area
        //});

        setTimeout(() => {
            if (window.Echo) {
                window.Echo.channel('pantheon-status')
                    .listen('TerminusCommandExecuted', (event) => {
                        console.log(event.message); // Log to console for debugging

                        // Display status message
                        const statusList = document.getElementById('statusList');
                        const listItem = document.createElement('li');
                        listItem.textContent = event.message;
                        statusList.appendChild(listItem);
                    });
            }
        }, 200);
    </script>
</body>
</html>
