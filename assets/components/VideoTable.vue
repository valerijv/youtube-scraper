<template>
  <v-card>
    <v-card-title>
      Youtube Videos
      <v-spacer></v-spacer>

      <v-select
        :items="tags"
        @change="tagSelected"
        @input.native="loadTags"
        :item-text="'name'"
        :item-value="'id'"
        label="Tag"
        autocomplete
        :clearable="true"
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
      :loading="loading"
    >
      <template slot="items" slot-scope="props">
        <td class="text-xs-right">{{ props.item.video_id }}</td>
        <td class="text-xs-right">{{ props.item.score }}</td>
        <td class="text-xs-right py-2">
          <div
            class="text-xs-center title"
          >
            {{ props.item.title }}
          </div>
          <div>
            <v-chip
              v-for="tag in props.item.tags"
              :key="tag.id"
            >{{ tag.name }}</v-chip>
          </div>
        </td>
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
  .py-2 {
    padding: 40px 0;
  }
</style>

<script>
  import axios from 'axios'

  export default {
    data () {
      return {
        tags: [],
        loading: false,
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
      this.getVideos('')
    },
    updated () {
      let input = document.querySelector('.input-group__icon-clearable')
      if (input) {
        input.addEventListener('click', () => {
          this.getVideos('')
        });
      }
    },
    methods: {
      getVideos (tag) {
        this.items = []
        this.loading = true
        const data = axios.get('videos?tag=' + tag)
          .then(data => {
            this.items = data.data
            this.loading = false
          })
      },
      loadTags (event) {
        if (event.target.value.length === 0) {
          this.getVideos('')
        } else {
          axios.get(`tags?query=${event.target.value}`)
            .then(data => {
              this.tags = data.data
            })
        }
      },
      tagSelected (tag) {
        if (typeof tag === 'number') {
          this.getVideos(tag)
        }
      }
    }
  }
</script>
