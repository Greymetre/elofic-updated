<x-app-layout>
   <style>
      .footer {
         display: none !important;
      }
   </style>
   <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
         {{ __('Dashboard') }}
      </h2>
   </x-slot>
   <div class="fr-container">
   <iframe title="dwerw (1)" width="100%" height="750" src="https://app.powerbi.com/view?r=eyJrIjoiMzRmOTk4NTQtMTQ0MC00YzUwLWIyZDktYjI4ZDJmMTZkYWFhIiwidCI6IjkyNTNmZjI5LWIxN2UtNDI1Zi1hNzg1LTg0MmRmYzAwZjQ1YyJ9" frameborder="1" allowFullScreen="true"></iframe>
   </div>
</x-app-layout>