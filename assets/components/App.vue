<template>
  <v-app id="inspire">
    <v-toolbar
      color="indigo"
      dark
      fixed
      app
    >
      <v-btn
        icon
        class="hidden-xs-only"
      >
        <v-icon>video_library</v-icon>
      </v-btn>
      <v-toolbar-title>Youtube Scraper</v-toolbar-title>
    </v-toolbar>
    <v-content>
      <v-container
        fluid
        fill-height
      >
        <v-layout
          justify-center
          align-center
        >
          <v-flex text-xs-center>
            <video-table></video-table>
          </v-flex>
        </v-layout>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
  import VideoTable from './VideoTable'

  export default {
    components: {
      VideoTable
    }
  }
</script>