<template>
  <v-card>
    <v-card-title>
      Youtube Videos
      <v-spacer></v-spacer>

      <v-select
        :items="states"
        v-model="tag"
        label="Tag"
        autocomplete
      ></v-select>

      <v-spacer></v-spacer>

      <v-text-field
        append-icon="search"
        label="Search"
        single-line
        hide-details
        v-model="search"
        class="search-i"
      ></v-text-field>

    </v-card-title>
    <v-data-table
      :headers="headers"
      :items="items"
      :search="search"
      :rows-per-page-items="rows"
    >
      <template slot="items" slot-scope="props">
        <td class="text-xs-right">{{ props.item.video_id }}</td>
        <td class="text-xs-right">{{ props.item.score }}</td>
        <td class="text-xs-right">{{ props.item.title }}</td>
        <td class="text-xs-right">{{ props.item.channel.name }} ({{ props.item.channel.channel_id }})</td>
        <td class="text-xs-right">{{ props.item.published }}</td>
      </template>
      <v-alert slot="no-results" :value="true" color="error" icon="warning">
        Your search for "{{ search }}" found no results.
      </v-alert>
    </v-data-table>
  </v-card>
</template>



<style lang="scss" scoped>
  .search-i {
    margin-top: -16px;
  }

</style>

<script>
  import axios from 'axios'

  export default {
    data () {
      return {
        states: ['1', 'asdsad', 'asdsddad'],
        tag: '',
        search: '',
        rows: [25,50,100],
        headers: [
          { text: 'Video ID', value: 'video_id', align: 'center' },
          { text: 'Performance', value: 'score', align: 'center' },
          { text: 'Title', value: 'title', align: 'center' },
          { text: 'Channel', value: 'channel', align: 'center' },
          { text: 'Published', value: 'published', align: 'center' },
        ],
        items: []
      }
    },
    mounted () {
      this.getVideos()
    },
    methods: {
      getVideos () {
        const data = axios.get('videos')
          .then(data => {
            this.items = data.data
          })
      }
    }
  }
</script>
