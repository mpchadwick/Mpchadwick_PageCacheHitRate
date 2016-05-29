#Mpchadwick_PageCacheHitRate

Hit rate tracking for Magento Page Cache.

###Configuration

All configuration is done through an XML file in the `/app/etc` directory. This is because module configuration is not loaded in the case of a page cache hit. `mpchadwick_pagecachehitrate.xml` is included with default settings. A few notes...

- `Mpchadwick_PageCacheHitRate_Model_Processor` is added as a `<request_processor>`.
  - **Note:** `request_processor`s are loaded alphabetically based on the file name in `/app/etc`. It is important that `Mpchadwick_PageCacheHitRate_Model_Processor` be the final `request_processor` to know for sure if this is a hit or miss. By default the `Enterprise_PageCache_Model_Processor` is defined in `enterprise.xml` and will be loaded first, however if your are using something else to process the result of `Enterprise_PageCache_Model_Processor` you may need to change the file name in `/app/etc`.
- A `<tracker>` can be configured your `<full_page_cache>` configuration. If this node is omitted, hit rate will not be tracked.

###Trackers

The following trackers are available...

- **`Mpchadwick_PageCacheHitRate_Model_Tracker_File`** A log file will be used for for logging hits / misses. A new file will be created each day.
- **`Mpchadwick_PageCacheHitRate_Model_Tracker_NewRelic`** Hits and misses will be tracked as [New Relic custom events](https://docs.newrelic.com/docs/insights/new-relic-insights/adding-querying-data/inserting-custom-events-new-relic-apm-agents). [NOTE: untested].

You can easily create your own tracker if you'd prefer a different means of tracking. Simply implement the `Mpchadwick_PageCacheHitRate_Model_TrackerInterface` interface and configure your class as the `<tracker>` in an xml file in `/app/etc`.

###Metrics

In addition to just tracking for hit / miss, route, URL and IP addresses are also tracked for each request. For example, you may want to see your cache hit rate for a single route such as `/catalog/product/view` or know your cache hit rate for a single IP address if your are seeing crawling / bot activity creating performance impact on your site.

