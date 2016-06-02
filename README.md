#Mpchadwick_PageCacheHitRate

Hit rate tracking for Magento Page Cache.

###Configuration

All configuration is done through an XML file in the `/app/etc` directory. This is because module configuration is not loaded in the case of a full hit. `mpchadwick_pagecachehitrate.xml` is included with default settings. A few notes...

- `Mpchadwick_PageCacheHitRate_Model_Processor` is added as a `<request_processor>`.
  - **Note:** `request_processor`s are loaded alphabetically based on the file name in `/app/etc`. It is important that `Mpchadwick_PageCacheHitRate_Model_Processor` be the final `request_processor` to know for sure if this is a full hit. By default the `Enterprise_PageCache_Model_Processor` is defined in `enterprise.xml` and will be loaded first, however if your are using something else to process the result of `Enterprise_PageCache_Model_Processor` you may need to change the file name in `/app/etc`.
- A `<tracker>` can be configured your `<full_page_cache>` configuration. If this node is omitted, hit rate will not be tracked.
- There is a `<track_container_misses>` setting which can be used to track individual container misses in the case of partial page hits.

###Trackers

The following trackers are available...

- **`Mpchadwick_PageCacheHitRate_Model_Tracker_File`** A log file will be used for for tracking hit rate. A new file will be created each day.
- **`Mpchadwick_PageCacheHitRate_Model_Tracker_NewRelic`** Hits and misses will be tracked as [New Relic custom events](https://docs.newrelic.com/docs/insights/new-relic-insights/adding-querying-data/inserting-custom-events-new-relic-apm-agents). [NOTE: untested].

You can easily create your own tracker if you'd prefer a different means of tracking. Simply implement the `Mpchadwick_PageCacheHitRate_Model_TrackerInterface` interface and configure your class as the `<tracker>` in an xml file in `/app/etc`.

###Metrics

In addition to a full hit and a full miss, `Enterprise_PageCache` can also have partial hits. This happens if a cached response is found, but it has containers that need additional processing. The `type` metric is thus tracked as either a `hit`, a `miss`, or a `partial`.

In addition to `type`, the following metrics are tracked for each response `route`, `url` and `ip` are also tracked for each request. For example, you may want to see your cache hit rate for a single route such as `/catalog/product/view` or know your cache hit rate for a single IP address if your are seeing crawling / bot activity creating performance impact on your site.

Here are some example entries using the `Mpchadwick_PageCacheHitRate_Model_Tracker_File` tracker...

```
2016-06-02T02:28:04+00:00 DEBUG (7): {"url":"http:\/\/magento-1_14_1_0.dev\/men\/shirts.html","ip":"172.16.9.1","type":"miss","route":"catalog\/category\/view"}
2016-06-02T02:28:09+00:00 DEBUG (7): {"url":"http:\/\/magento-1_14_1_0.dev\/men\/shirts.html","ip":"172.16.9.1","type":"partial","route":"catalog\/category\/view"}
2016-06-02T02:28:11+00:00 DEBUG (7): {"url":"http:\/\/magento-1_14_1_0.dev\/men\/shirts.html","ip":"172.16.9.1","type":"hit","route":"catalog\/category\/view"}
```

Container misses will be recorded to a separate file (if enabled). The entries will look like this...

```
2016-06-02T03:18:14+00:00 DEBUG (7): {"url":"http:\/\/magento-1_14_1_0.dev\/women\/women-new-arrivals.html","ip":"172.16.9.1","route":"catalog\/category\/view","container":"Enterprise_PageCache_Model_Container_Catalognavigation"}
```
